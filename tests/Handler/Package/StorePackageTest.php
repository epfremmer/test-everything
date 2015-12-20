<?php
/**
 * File StorePackageTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Package;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Handler\Package\StorePackage;
use Epfremme\Everything\Tests\Entity\PackageTest;
use Epfremme\Everything\Tests\Traits\SerializerTrait;

/**
 * Class StorePackageTest
 *
 * @package Epfremme\Everything\Tests\Handler\Package
 */
class StorePackageTest extends \PHPUnit_Framework_TestCase
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

        $this->packages = new Collection();
    }

    public function testConstruct()
    {
        $handler = new StorePackage($this->packages);

        $this->assertAttributeSame($this->packages, 'packages', $handler);
    }

    public function testInvoke()
    {
        $handler = new StorePackage($this->packages);
        $package = $this->getSerializer()->deserialize(PackageTest::TEST_PACKAGE_JSON, Package::class, 'json');

        $this->assertSame($package, $handler($package));
        $this->assertCount(1, $this->packages);
        $this->assertSame($package, $this->packages->get('test/test'));
    }
}
