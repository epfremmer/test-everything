<?php
/**
 * File Cache.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\FileSystem;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Json;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class Cache
 *
 * @package Epfremme\Everything\FileSystem
 */
class Cache
{
    const CACHE_DIR = '.cache';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Collection
     */
    private $configs;

    /**
     * Cache constructor
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->configs = new Collection();

        $this->initCacheDir();
    }

    /**
     * Initialize cache directory
     *
     * @return void
     */
    public function initCacheDir()
    {
        if (!$this->fs->exists(self::CACHE_DIR)) {
            $this->fs->mkdir(self::CACHE_DIR);
        }
    }

    public function addConfig(Json $config)
    {
        $json = $config->toJson();
        $hash = sha1($json);
        $file = sprintf('%s/%s/composer.json', self::CACHE_DIR, $hash);

        if (!$this->fs->exists($file)) {
            $this->fs->mkdir(sprintf('%s/%s', self::CACHE_DIR, $hash));
            $this->fs->dumpFile($file, $json);
        }
    }

    public function link($path)
    {
        $finder = new Finder();
        $finder->directories()->in(self::CACHE_DIR);

        foreach ($finder as $key => $directory) {
            /** @var \SplFileInfo $directory */
            if ($directory->isLink()) {
                continue;
            }

            $relativePath = $this->fs->makePathRelative($path, (string) $directory);
            $this->fs->symlink($relativePath, sprintf('%s/%s', $directory, rtrim($path, '/')));
        }
    }
}
