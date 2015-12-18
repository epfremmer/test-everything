<?php
/**
 * File PackageTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Entity;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Subscriber\SerializationSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

/**
 * Class PackageTest
 *
 * @package Epfremme\Everything\Tests\Entity
 */
class PackageTest extends \PHPUnit_Framework_TestCase
{
    const TEST_PACKAGE_JSON = '{"package":{"name":"test/test","description":"test description","time":"2015-12-07T00:13:33+0000","maintainers":[{"name": "epfremmer"}],"versions":{"dev-master":{"name":"test/test","version":"dev-master"}},"type":"library","repository":"https://github.com/test/test.git","downloads":{"total":1,"monthly":1,"daily":0},"favers":1}}';

    /**
     * @var Package
     */
    private $package;

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

        AnnotationRegistry::registerLoader('class_exists');

        $serializerBuilder = new SerializerBuilder();
        $serializerBuilder->configureListeners(function(EventDispatcher $eventDispatcher) {
            $eventDispatcher->addSubscriber(new SerializationSubscriber());
        });

        $this->serializer = $serializerBuilder->build();

        $reflectionClass = new \ReflectionClass(Package::class);

        $this->package = new Package();

        $name = $reflectionClass->getProperty('name');
        $description = $reflectionClass->getProperty('description');
        $time = $reflectionClass->getProperty('time');
        $maintainers = $reflectionClass->getProperty('maintainers');
        $versions = $reflectionClass->getProperty('versions');
        $type = $reflectionClass->getProperty('type');
        $repository = $reflectionClass->getProperty('repository');
        $downloads = $reflectionClass->getProperty('downloads');
        $favers = $reflectionClass->getProperty('favers');

        $name->setAccessible(true);
        $description->setAccessible(true);
        $time->setAccessible(true);
        $maintainers->setAccessible(true);
        $versions->setAccessible(true);
        $type->setAccessible(true);
        $repository->setAccessible(true);
        $downloads->setAccessible(true);
        $favers->setAccessible(true);

        $name->setValue($this->package, 'test/test');
        $description->setValue($this->package, 'test description');
        $time->setValue($this->package, new \DateTime('2015-12-07T00:13:33+0000'));
        $maintainers->setValue($this->package, [['name' => 'epfremmer']]);
        $versions->setValue($this->package, ['dev-master' => ['name' => 'test/test', 'version' => 'dev-master']]);
        $type->setValue($this->package, 'library');
        $repository->setValue($this->package, 'https://github.com/test/test.git');
        $downloads->setValue($this->package, ['total' => 1, 'monthly' => 1, 'daily' => 0]);
        $favers->setValue($this->package, 1);
    }

    public function testSerialization()
    {
        $packageJson = $this->serializer->serialize(['package' => $this->package], 'json');

        $this->assertJsonStringEqualsJsonString(self::TEST_PACKAGE_JSON, $packageJson);
    }

    public function testDeserialization()
    {
        $package = $this->serializer->deserialize(self::TEST_PACKAGE_JSON, Package::class, 'json');

        $this->assertInstanceOf(Package::class, $package);
        $this->assertEquals($this->package, $package);
    }

    public function testGetName()
    {
        $this->assertEquals('test/test', $this->package->getName());
    }

    public function testGetDescription()
    {
        $this->assertEquals('test description', $this->package->getDescription());
    }

    public function testGetTime()
    {
        $this->assertInstanceOf(\DateTime::class, $this->package->getTime());
        $this->assertEquals(new \DateTime('2015-12-07T00:13:33+0000'), $this->package->getTime());
    }

    public function testGetMaintainers()
    {
        $this->assertEquals([['name' => 'epfremmer']], $this->package->getMaintainers());
    }

    public function testGetVersions()
    {
        $this->assertEquals(['dev-master' => ['name' => 'test/test', 'version' => 'dev-master']], $this->package->getVersions());
    }

    public function testGetType()
    {
        $this->assertEquals('library', $this->package->getType());
    }

    public function testGetRepository()
    {
        $this->assertEquals('https://github.com/test/test.git', $this->package->getRepository());
    }

    public function testGetDownloads()
    {
        $this->assertEquals(['total' => 1, 'monthly' => 1, 'daily' => 0], $this->package->getDownloads());
    }

    public function testGetFavers()
    {
        $this->assertEquals(1, $this->package->getFavers());
    }

    public function testSetVersions()
    {
        $this->package->setVersions([]);

        $this->assertEquals([], $this->package->getVersions());
    }
}
