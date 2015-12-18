<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:42 AM
 */

namespace Epfremme\Everything\Counter;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;

class PackageVersionCounter implements \Countable
{
    /**
     * @var Collection
     */
    private $packages;

    /**
     * CountPackageVersions constructor
     *
     * @param Collection $packages
     */
    public function __construct(Collection $packages)
    {
        $this->packages = $packages;
    }

    public function count()
    {
        return $this->packages->reduce(function($count, Package $package) {
            return $count + count($package->getVersions());
        });
    }
}
