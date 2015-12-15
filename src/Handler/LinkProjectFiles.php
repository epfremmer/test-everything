<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 11:19 AM
 */

namespace Epfremme\Everything\Handler;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Json;
use Epfremme\Everything\FileSystem\Cache;

class LinkProjectFiles
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
     * LinkProjectFiles constructor
     *
     * @param Cache $cache
     * @param Json $json
     */
    public function __construct(Cache $cache, Json $json)
    {
        $this->cache = $cache;
        $this->json = $json;
    }

    /**
     * @param Collection $packages
     * @return Collection
     */
    public function __invoke(Collection $packages)
    {
        foreach ((array)$this->json->get('autoload') as $psr) {
            foreach ($psr as $namespace => $path) {
                $this->cache->link($path);
            }
        }

        return $packages;
    }
}
