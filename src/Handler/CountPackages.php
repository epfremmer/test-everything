<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:42 AM
 */

namespace Epfremme\Everything\Handler;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Package;

class CountPackages
{
    public function __invoke(Collection $packages)
    {
        return $packages->reduce(function($count, Package $package) {
            return $count + count($package->getVersions());
        });
    }
}
