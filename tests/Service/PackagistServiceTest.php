<?php
/**
 * File PackagistServiceTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Service;

use Epfremme\Everything\Service\PackagistService;
use Epfremme\Everything\Tests\Entity\PackageTest;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class PackagistServiceTest
 *
 * @package Epfremme\Everything\Tests\Service
 */
class PackagistServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClientInterface|\Mockery\MockInterface
     */
    private $client;

    /**
     * Mock the existing service client
     *
     * @param PackagistService $service
     * @return PackagistService
     */
    private function mockClient(PackagistService $service)
    {
        $this->client = \Mockery::mock(Client::class);

        $reflectionClass = new \ReflectionClass(PackagistService::class);
        $clientProperty = $reflectionClass->getProperty('client');

        $clientProperty->setAccessible(true);
        $clientProperty->setValue($service, $this->client);

        return $service;
    }

    public function testConstruct()
    {
        $service = new PackagistService();

        $this->assertAttributeInstanceOf(ClientInterface::class, 'client', $service);
    }

    public function testGetPackage()
    {
        $service = $this->mockClient(new PackagistService());
        $response = new Response(200, [], PackageTest::TEST_PACKAGE_JSON);

        $this->client->shouldReceive('getAsync')->once()->with('/packages/test/test.json')->andReturn($response);

        /** @var Response $package */
        $package = $service->getPackage('test/test');

        $this->assertInstanceOf(ResponseInterface::class, $package);
        $this->assertInstanceOf(StreamInterface::class, $package->getBody());
        $this->assertEquals(PackageTest::TEST_PACKAGE_JSON, $package->getBody());
    }
}
