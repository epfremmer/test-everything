<?php
/**
 * File GetBaseConfigurationTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Setup;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Handler\Package\FilterPackageVersions;
use Epfremme\Everything\Handler\Setup\GetBaseConfiguration;
use Epfremme\Everything\Tests\Entity\PackageTest;
use Epfremme\Everything\Tests\Traits\SerializerTrait;

/**
 * Class GetBaseConfigurationTest
 *
 * @package Epfremme\Everything\Tests\Handler\Setup
 */
class GetBaseConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use SerializerTrait;

    /**
     * @var Collection
     */
    private $packages;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $handler = new FilterPackageVersions('^1.0');
        $serializer = $this->getSerializer();

        $this->packages = new Collection([
            'a/a' => $serializer->deserialize(str_replace('test/test', 'a/a', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
            'b/b' => $serializer->deserialize(str_replace('test/test', 'b/b', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
            'c/c' => $serializer->deserialize(str_replace('test/test', 'c/c', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
        ]);

        $this->packages->each(function(Package $package) use ($handler) {
            $handler($package);
        });
    }

    public function testConstruct()
    {
        $handler = new GetBaseConfiguration($this->packages);

        $this->assertSame($this->packages, 'packages', $handler);
    }

    public function testInvoke()
    {
        $handler = new GetBaseConfiguration($this->packages);
        $config = $handler();
        $expected = new \ArrayObject([
            'a/a' => "1.0.0",
            'b/b' => "1.0.0",
            'c/c' => "1.0.0",
        ]);

        $this->assertInstanceOf(\ArrayObject::class, $config);
        $this->assertCount(3, $config);
        $this->assertEquals($expected, $config);
    }
}
