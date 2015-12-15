<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:32 AM
 */

namespace Epfremme\Everything\Handler;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Package;

class SortPackages
{
    /**
     * @param Collection $packages
     * @return Collection
     */
    public function __invoke(Collection $packages)
    {
        $packages->asort(function(Package $a, Package $b) {
            return $a->getName() > $b->getName() ? 1 : -1;
        });

        return $packages;
    }
}
