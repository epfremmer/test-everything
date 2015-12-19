<?php
/**
 * File PackageVersionCounterTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Counter;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Counter\PackageVersionCounter;
use Epfremme\Everything\Entity\Package;

/**
 * Class PackageVersionCounterTest
 *
 * @package Epfremme\Everything\Tests\Counter
 */
class PackageVersionCounterTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $packages = $this->getPackages();
        $packageCounter = new PackageVersionCounter($packages);

        $this->assertAttributeSame($packages, 'packages', $packageCounter);
    }

    public function testCount()
    {
        $packages = $this->getPackages();
        $packageCounter = new PackageVersionCounter($packages);

        $this->assertEquals(6, $packageCounter->count());
    }

    /**
     * @return Collection
     */
    private function getPackages()
    {
        return new Collection([
            (new Package())->setVersions(['a', 'b', 'c']),
            (new Package())->setVersions(['a']),
            (new Package())->setVersions(['a', 'b']),
        ]);
    }
}
