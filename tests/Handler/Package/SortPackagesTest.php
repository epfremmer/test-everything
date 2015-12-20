<?php
/**
 * File SortPackagesTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Package;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Handler\Package\SortPackages;
use Epfremme\Everything\Tests\Entity\PackageTest;
use Epfremme\Everything\Tests\Traits\SerializerTrait;


/**
 * Class SortPackagesTest
 *
 * @package Epfremme\Everything\Tests\Handler\Package
 */
class SortPackagesTest extends \PHPUnit_Framework_TestCase
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

        $serializer = $this->getSerializer();

        $this->packages = new Collection([
            'b/b' => $serializer->deserialize(str_replace('test/test', 'b/b', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
            'a/a' => $serializer->deserialize(str_replace('test/test', 'a/a', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
            'c/c' => $serializer->deserialize(str_replace('test/test', 'c/c', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
        ]);
    }

    public function testConstruct()
    {
        $handler = new SortPackages($this->packages);

        $this->assertAttributeSame($this->packages, 'packages', $handler);
    }

    public function testInvoke()
    {
        $handler = new SortPackages($this->packages);
        $packages = $handler();

        $this->assertSame($this->packages, $packages);
        $this->assertEquals('a/a', $this->packages->current()->getName());
        $this->assertEquals('b/b', $this->packages->next()->getName());
        $this->assertEquals('c/c', $this->packages->next()->getName());
        $this->assertEquals(['a/a', 'b/b', 'c/c'], $this->packages->getKeys());
    }
}
