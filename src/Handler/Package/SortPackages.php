<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:32 AM
 */

namespace Epfremme\Everything\Handler\Package;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;

class SortPackages
{
    /**
     * @var Collection
     */
    private $packages;

    /**
     * SortPackages constructor
     *
     * @param Collection $packages
     */
    public function __construct(Collection $packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return Collection
     */
    public function __invoke()
    {
        $this->packages->asort(function(Package $a, Package $b) {
            return $a->getName() > $b->getName() ? 1 : -1;
        });

        return $this->packages;
    }
}
