<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:42 AM
 */

namespace Epfremme\Everything\Handler\Package;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;

class CountPackageVersions
{
    /**
     * @var Collection|null
     */
    private $packages;

    /**
     * CountPackageVersions constructor
     *
     * @param Collection|null $packages
     */
    public function __construct(Collection $packages)
    {
        $this->packages = $packages;
    }

    public function __invoke()
    {
        return $this->packages->reduce(function($count, Package $package) {
            return $count + count($package->getVersions());
        });
    }
}
