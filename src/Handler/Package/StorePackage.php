<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 9:12 PM
 */

namespace Epfremme\Everything\Handler\Package;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Filesystem\Cache;

class StorePackage
{
    /**
     * @var Cache
     */
    private $packages;

    /**
     * InstallPackageVersionsHandler constructor
     *
     * @param Collection $packages
     */
    public function __construct(Collection $packages)
    {
        $this->packages = $packages;
    }

    public function __invoke(Package $package)
    {
        $this->packages->set($package->getName(), $package);

        return $package;
    }
}
