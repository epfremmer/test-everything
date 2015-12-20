<?php
/**
 * File DeserializePackageTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Package;

use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Handler\Package\DeserializePackage;
use Epfremme\Everything\Tests\Entity\PackageTest;
use Epfremme\Everything\Tests\Traits\SerializerTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DeserializePackageTest
 *
 * @package Epfremme\Everything\Tests\Handler\Package
 */
class DeserializePackageTest extends \PHPUnit_Framework_TestCase
{
    use SerializerTrait;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->serializer = $this->getSerializer();
    }

    public function testConstruct()
    {
        $handler = new DeserializePackage($this->serializer);

        $this->assertAttributeSame($this->serializer, 'serializer', $handler);
    }

    public function testInvoke()
    {
        $response = \Mockery::mock(ResponseInterface::class);
        $handler = new DeserializePackage($this->serializer);

        $response->shouldReceive('getBody')->once()->andReturn(PackageTest::TEST_PACKAGE_JSON);

        $this->assertInstanceOf(Package::class, $handler($response));
    }
}
