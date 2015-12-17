<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:50 AM
 */

namespace Epfremme\Everything\Handler\Setup;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\Package;

class GetBaseConfiguration
{
    /**
     * @var Collection
     */
    private $packages;

    /**
     * GetBaseConfiguration constructor
     *
     * @param Collection $packages
     */
    public function __construct(Collection $packages)
    {
        $this->packages = $packages;
    }

    /**
     * @return \ArrayObject
     */
    public function __invoke()
    {
        return $this->packages->reduce(function($result, Package $package) {
            $versions = $package->getVersions();
            $version = max(array_keys($versions));
            $result = $result ?: new \ArrayObject();

            $result[$package->getName()] = $version;

            return $result;
        });
    }
}
