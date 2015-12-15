<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:52 AM
 */

namespace Epfremme\Everything\Handler;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Json;
use Epfremme\Everything\Composer\Package;
use Epfremme\Everything\FileSystem\Cache;
use Symfony\Component\Console\Helper\ProgressBar;

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
     * @var ProgressBar
     */
    private $progress;

    /**
     * @var Json
     */
    private $json;

    /**
     * GetBaseConfiguration constructor
     *
     * @param Collection $packages
     * @param Cache $cache
     * @param ProgressBar $progress
     * @param Json $json
     */
    public function __construct(Collection $packages, Cache $cache, ProgressBar $progress, Json $json)
    {
        $this->packages = $packages;
        $this->cache = $cache;
        $this->progress = $progress;
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
                $baseConfig = $base->getArrayCopy();

                $baseConfig[$package->getName()] = $version;

                $this->json->setRequire($baseConfig);

                $this->cache->addConfig($this->json);
                $this->progress->advance();
            }
        });

        return $this->packages;
    }
}
