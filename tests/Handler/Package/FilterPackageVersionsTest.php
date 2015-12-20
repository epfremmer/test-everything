<?php
/**
 * File FilterPackageVersionsTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Package;

use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Handler\Package\FilterPackageVersions;
use Epfremme\Everything\Subscriber\SerializationSubscriber;
use Epfremme\Everything\Tests\Entity\PackageTest;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * Class FilterPackageVersionsTest
 *
 * @package Epfremme\Everything\Tests\Handler\Package
 */
class FilterPackageVersionsTest extends \PHPUnit_Framework_TestCase
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
        $handler = new FilterPackageVersions('^1.0');

        $this->assertAttributeEquals('^1.0', 'constraint', $handler);
    }

    public function testInvoke()
    {
        $handler = new FilterPackageVersions('^1.0');
        $package = $this->serializer->deserialize(PackageTest::TEST_PACKAGE_JSON, Package::class, 'json');
        $expected = [
            '1.0.0' => ['name' => 'test/test', 'version' => '1.0.0']
        ];

        $this->assertSame($package, $handler($package));
        $this->assertEquals($expected, $package->getVersions());
        $this->assertArrayNotHasKey('dev-master', $package->getVersions());
        $this->assertArrayNotHasKey('1.0.0-beta', $package->getVersions());
        $this->assertNotContains(['name' => 'test/test', 'version' => 'dev-master'], $package->getVersions());
        $this->assertNotContains(['name' => 'test/test', 'version' => '1.0.0-beta'], $package->getVersions());
    }
}
