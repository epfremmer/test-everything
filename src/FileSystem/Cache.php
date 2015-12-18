<?php
/**
 * File Cache.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\FileSystem;

use Epfremme\Everything\Composer\Json;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class Cache
 *
 * @package Epfremme\Everything\FileSystem
 */
class Cache implements \Countable
{
    const CACHE_DIR = '.cache';

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * Cache constructor
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->finder = new Finder();

        $this->finder->directories()->in(self::CACHE_DIR)->depth(0);
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
        $lock = sprintf('%s/%s/composer.lock', self::CACHE_DIR, $hash);

        if ($this->fs->exists($lock)) {
            $this->fs->remove($lock);
        }

        if (!$this->fs->exists($file)) {
            $this->fs->mkdir(sprintf('%s/%s', self::CACHE_DIR, $hash));
            $this->fs->dumpFile($file, $json);
        }

        return $hash;
    }

    public function mirror(array $paths, \Closure $callback = null)
    {
        $this->each(function(SplFileInfo $directory) use ($paths, $callback) {
            foreach ($paths as $path) {
                $relativePath = $this->fs->makePathRelative($path, (string) $directory);
                $originPath = realpath(sprintf('%s/%s', $directory->getRealPath(), $relativePath));
                $targetPath = sprintf('%s/%s', $directory->getRealPath(), $path);

                $this->fs->mirror($originPath, $targetPath, null, [
                    'override' => true,
                    'delete' => true,
                ]);
            }

            $callback();
        });
    }

    public function each(\Closure $fn)
    {
        foreach ($this->finder as $key => $directory) {
            if ($fn($directory, $key) === false) {
                return false;
            };
        }

        return true;
    }

    public function count()
    {
        return $this->finder->count();
    }
}
