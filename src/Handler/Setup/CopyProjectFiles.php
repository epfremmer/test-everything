<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 11:19 AM
 */

namespace Epfremme\Everything\Handler\Setup;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Json;
use Epfremme\Everything\Filesystem\Cache;
use Symfony\Component\Console\Helper\ProgressBar;

class CopyProjectFiles
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var ProgressBar
     */
    private $progress;

    /**
     * LinkProjectFiles constructor
     *
     * @param Cache $cache
     * @param Json $json
     * @param ProgressBar $progress
     */
    public function __construct(Cache $cache, Json $json, ProgressBar $progress)
    {
        $this->cache = $cache;
        $this->json = $json;
        $this->progress = $progress;
    }

    /**
     * @param Collection $packages
     * @return Collection
     */
    public function __invoke(Collection $packages)
    {
        $paths = array_reduce((array) $this->json->get('autoload'), function($paths, array $psr) {
            return array_merge((array) $paths, array_values($psr));
        });

        $this->cache->mirror($paths, function() {
            $this->progress->advance();
            $this->progress->display();
        });
    }
}
