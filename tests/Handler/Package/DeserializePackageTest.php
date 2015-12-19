<?php
/**
 * File DeserializePackageTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Package;

use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Handler\Package\DeserializePackage;
use Epfremme\Everything\Subscriber\SerializationSubscriber;
use Epfremme\Everything\Tests\Entity\PackageTest;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Psr\Http\Message\ResponseInterface;

/**
 * Class DeserializePackageTest
 *
 * @package Epfremme\Everything\Tests\Handler\Package
 */
class DeserializePackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $serializerBuilder = new SerializerBuilder();
        $serializerBuilder->configureListeners(function(EventDispatcher $eventDispatcher) {
            $eventDispatcher->addSubscriber(new SerializationSubscriber());
        });

        $this->serializer = $serializerBuilder->build();
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
