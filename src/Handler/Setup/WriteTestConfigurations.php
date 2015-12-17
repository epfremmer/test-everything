<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:52 AM
 */

namespace Epfremme\Everything\Handler\Setup;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Json;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\FileSystem\Cache;

class WriteTestConfigurations
{
    /**
     * @var Collection
     */
    private $packages;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Json
     */
    private $json;

    /**
     * GetBaseConfiguration constructor
     *
     * @param Collection $packages
     * @param Cache $cache
     * @param Json $json
     */
    public function __construct(Collection $packages, Cache $cache, Json $json)
    {
        $this->packages = $packages;
        $this->cache = $cache;
        $this->json = $json;
    }

    /**
     * @param \ArrayObject $base
     * @return Collection
     */
    public function __invoke(\ArrayObject $base)
    {
        $this->packages->each(function(Package $package) use ($base) {
            foreach ($package->getVersions() as $version => $info) {
                $require = $base->getArrayCopy();

                $require[$package->getName()] = $version;

                $this->json->setRequire($require);
                $this->cache->addConfig($this->json);
            }
        });

        return $this->packages;
    }
}
