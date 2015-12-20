<?php
/**
 * File FilterPackageVersionsTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Package;

use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Handler\Package\FilterPackageVersions;
use Epfremme\Everything\Tests\Entity\PackageTest;
use Epfremme\Everything\Tests\Traits\SerializerTrait;

/**
 * Class FilterPackageVersionsTest
 *
 * @package Epfremme\Everything\Tests\Handler\Package
 */
class FilterPackageVersionsTest extends \PHPUnit_Framework_TestCase
{
    use SerializerTrait;

    public function testConstruct()
    {
        $handler = new FilterPackageVersions('^1.0');

        $this->assertAttributeEquals('^1.0', 'constraint', $handler);
    }

    public function testInvoke()
    {
        $handler = new FilterPackageVersions('^1.0');
        $package = $this->getSerializer()->deserialize(PackageTest::TEST_PACKAGE_JSON, Package::class, 'json');
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
