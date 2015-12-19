<?php
/**
 * File CacheTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Filesystem;

use Epfremme\Everything\Filesystem\Cache;
use Epfremme\Everything\Composer\Json;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class CacheTest
 *
 * @package Epfremme\Everything\Tests\Filesystem
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem|\Mockery\MockInterface
     */
    private $fs;

    /**
     * @var Finder|\Mockery\MockInterface
     */
    private $finder;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fs = \Mockery::mock(Filesystem::class);
        $this->finder = \Mockery::mock(Finder::class);
    }

    /**
     * @return Cache
     */
    private function getCache()
    {
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->finder->shouldReceive('directories', 'in', 'depth')->once()->andReturnSelf();

        return new Cache($this->fs, $this->finder);
    }

    /**
     * @param int $count
     * @return \ArrayObject
     */
    private function getIterator($count = 0)
    {
        $directory = \Mockery::mock(SplFileInfo::class);
        $iterator = new \ArrayObject();

        for($i =0; $i < $count; $i++) {
            $iterator->append($directory);
        }

        return $iterator;
    }

    public function testConstruct()
    {
        $this->fs->shouldReceive('exists')->once()->with(Cache::CACHE_DIR)->andReturn(false);
        $this->fs->shouldReceive('mkdir')->once()->with(Cache::CACHE_DIR);
        $this->finder->shouldReceive('directories')->once()->andReturnSelf();
        $this->finder->shouldReceive('in')->once()->with(Cache::CACHE_DIR)->andReturnSelf();
        $this->finder->shouldReceive('depth')->once()->with(0)->andReturnSelf();

        $cache = new Cache($this->fs, $this->finder);

        $this->assertAttributeInstanceOf(Filesystem::class, 'fs', $cache);
        $this->assertAttributeInstanceOf(Finder::class, 'finder', $cache);
    }

    public function testConstructWithExistingCache()
    {
        $this->fs->shouldReceive('exists')->once()->with(Cache::CACHE_DIR)->andReturn(true);
        $this->fs->shouldNotReceive('mkdir')->with(Cache::CACHE_DIR);
        $this->finder->shouldReceive('directories')->once()->andReturnSelf();
        $this->finder->shouldReceive('in')->once()->with(Cache::CACHE_DIR)->andReturnSelf();
        $this->finder->shouldReceive('depth')->once()->with(0)->andReturnSelf();

        $cache = new Cache($this->fs, $this->finder);

        $this->assertAttributeInstanceOf(Filesystem::class, 'fs', $cache);
        $this->assertAttributeInstanceOf(Finder::class, 'finder', $cache);
    }

    public function testAddConfig()
    {
        $config = new Json();

        $path = sprintf('%s/%s', Cache::CACHE_DIR, sha1($config));
        $file = sprintf('%s/composer.json', $path);
        $lock = sprintf('%s/composer.lock', $path);

        $this->fs->shouldReceive('exists')->once()->with($lock)->andReturn(false);
        $this->fs->shouldNotReceive('remove');
        $this->fs->shouldReceive('exists')->once()->with($file)->andReturn(false);
        $this->fs->shouldReceive('mkdir')->once()->with($path);
        $this->fs->shouldReceive('dumpFile')->once()->with($file, $config);

        $hash = $this->getCache()->addConfig($config);

        $this->assertEquals($hash, sha1($config));
    }

    public function testAddConfigWithExistingFiles()
    {
        $config = new Json();

        $path = sprintf('%s/%s', Cache::CACHE_DIR, sha1($config));
        $file = sprintf('%s/composer.json', $path);
        $lock = sprintf('%s/composer.lock', $path);

        $this->fs->shouldReceive('exists')->once()->with($lock)->andReturn(true);
        $this->fs->shouldReceive('remove')->once()->with($lock);
        $this->fs->shouldReceive('exists')->once()->with($file)->andReturn(true);
        $this->fs->shouldNotReceive('mkdir');
        $this->fs->shouldNotReceive('dumpFile');

        $hash = $this->getCache()->addConfig($config);

        $this->assertEquals($hash, sha1($config));
    }

    public function testEach()
    {
        $this->finder->shouldReceive('getIterator')->andReturn($this->getIterator(5));

        $index = 0;
        $result = $this->getCache()->each(function($directory, $key) use (&$index) {
            $this->assertInstanceOf(SplFileInfo::class, $directory);
            $this->assertEquals($index, $key);

            $index++;
        });

        $this->assertTrue($result);
    }

    public function testEachReturnEarly()
    {
        $this->finder->shouldReceive('getIterator')->once()->withNoArgs()->andReturn($this->getIterator(5));

        $result = $this->getCache()->each(function() {
            return false;
        });

        $this->assertFalse($result);
    }

    public function testCount()
    {
        $cache = $this->getCache();

        $this->assertInstanceOf(\Countable::class, $cache);

        $this->finder->shouldReceive('count')->passthru();
        $this->finder->shouldReceive('getIterator')->andReturn($this->getIterator(5));

        $this->assertEquals(5, $cache->count());
        $this->assertCount(5, $cache);
    }

    public function testMirror()
    {
        $cache = $this->getCache();
        $iterator = $this->getIterator(5);

        $originDir = getcwd();
        $targetDir = join('/', ['/tmp', sha1('')]);
        $options = ['override' => true, 'delete' => true];

        /** @var SplFileInfo|\Mockery\MockInterface $directory */
        $directory = $iterator->getIterator()->current();

        $directory->shouldReceive('getRealPath')->times(10)->withNoArgs()->andReturn($targetDir);
        $this->finder->shouldReceive('getIterator')->andReturn($iterator);

        $this->fs->shouldReceive('mirror')->times(5)->with(
            join('/',[$originDir, 'src/']),
            join('/', [$targetDir, 'src/']),
            null,
            $options
        );

        $this->fs->shouldReceive('mirror')->times(5)->with(
            join('/',[$originDir, 'tests/']),
            join('/', [$targetDir, 'tests/']),
            null,
            $options
        );

        $cache->mirror(['src/', 'tests/']);
    }

    public function testMirrorWithCallback()
    {
        $cache = $this->getCache();
        $iterator = $this->getIterator(5);

        $originDir = getcwd();
        $targetDir = join('/', ['/tmp', sha1('')]);
        $options = ['override' => true, 'delete' => true];

        /** @var SplFileInfo|\Mockery\MockInterface $directory */
        $directory = $iterator->getIterator()->current();

        $directory->shouldReceive('getRealPath')->times(10)->withNoArgs()->andReturn($targetDir);
        $this->finder->shouldReceive('getIterator')->andReturn($iterator);

        $this->fs->shouldReceive('mirror')->times(5)->with(
            join('/',[$originDir, 'src/']),
            join('/', [$targetDir, 'src/']),
            null,
            $options
        );

        $this->fs->shouldReceive('mirror')->times(5)->with(
            join('/',[$originDir, 'tests/']),
            join('/', [$targetDir, 'tests/']),
            null,
            $options
        );

        $callCount = 0;

        $cache->mirror(['src/', 'tests/'], function() use (&$callCount) {
            $callCount++;
        });

        $this->assertEquals(5, $callCount);
    }
}
