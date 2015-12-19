<?php
/**
 * File CacheTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Filesystem;

use Epfremme\Everything\Filesystem\Cache;
use Epfremme\Everything\Composer\Json;
use Mockery;
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
     * @var Filesystem|Mockery\MockInterface
     */
    private $fs;

    /**
     * @var Finder|Mockery\MockInterface
     */
    private $finder;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fs = Mockery::mock(Filesystem::class);
        $this->finder = Mockery::mock(Finder::class);
    }

    private function getCache()
    {
        $this->fs->shouldReceive('exists')->once()->andReturn(true);
        $this->finder->shouldReceive('directories', 'in', 'depth')->once()->andReturnSelf();

        return new Cache($this->fs, $this->finder);
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
        $directory = Mockery::mock(SplFileInfo::class);
        $iterator = Mockery::mock(\Iterator::class);

        $this->finder->shouldReceive('getIterator')->once()->withNoArgs()->andReturn($iterator);

        $iterator->shouldReceive('rewind')->once()->withNoArgs();
        $iterator->shouldReceive('next')->times(5)->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('current')->times(5)->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('key')->times(5)->withNoArgs()->andReturn('key');
        $iterator->shouldReceive('valid')->times(5)->withNoArgs()->andReturn(true);
        $iterator->shouldReceive('valid')->once()->withNoArgs()->andReturn(false);

        $result = $this->getCache()->each(function($directory, $key) {
            $this->assertInstanceOf(SplFileInfo::class, $directory);
            $this->assertEquals('key', $key);
        });

        $this->assertTrue($result);
    }

    public function testEachReturnEarly()
    {
        $directory = Mockery::mock(SplFileInfo::class);
        $iterator = Mockery::mock(\Iterator::class);

        $this->finder->shouldReceive('getIterator')->once()->withNoArgs()->andReturn($iterator);

        $iterator->shouldReceive('rewind')->once()->withNoArgs();
        $iterator->shouldReceive('next')->once()->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('current')->once()->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('key')->once()->withNoArgs()->andReturn('key');
        $iterator->shouldReceive('valid')->once()->withNoArgs()->andReturn(true);

        $result = $this->getCache()->each(function() {
            return false;
        });

        $this->assertFalse($result);
    }

    public function testCount()
    {
        $directory = Mockery::mock(SplFileInfo::class);
        $iterator = Mockery::mock(\Iterator::class);

        $cache = $this->getCache();

        $this->assertInstanceOf(\Countable::class,$cache);

        $this->finder->shouldReceive('count')->passthru();
        $this->finder->shouldReceive('getIterator')->andReturn($iterator);

        $iterator->shouldReceive('rewind')->once()->withNoArgs();
        $iterator->shouldReceive('next')->times(5)->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('current')->times(5)->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('key')->times(5)->withNoArgs()->andReturn('key');
        $iterator->shouldReceive('valid')->times(5)->withNoArgs()->andReturn(true);
        $iterator->shouldReceive('valid')->once()->withNoArgs()->andReturn(false);

        $this->assertEquals(5, $cache->count());

        $iterator->shouldReceive('rewind')->once()->withNoArgs();
        $iterator->shouldReceive('next')->times(5)->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('current')->times(5)->withNoArgs()->andReturn($directory);
        $iterator->shouldReceive('key')->times(5)->withNoArgs()->andReturn('key');
        $iterator->shouldReceive('valid')->times(5)->withNoArgs()->andReturn(true);
        $iterator->shouldReceive('valid')->once()->withNoArgs()->andReturn(false);

        $this->assertCount(5, $cache);
    }

//    public function testMirror()
//    {
//        $this->assertTrue(true);
//        $cache = $this->getCache();
//        var_dump(__DIR__);exit;
//
//
//
//        $this->fs->shouldReceive('makePathRelative')->times(5)->with('/src', __DIR__)->andReturn('');
//        $this->fs->shouldReceive('makePathRelative')->times(5)->with('/tests', __DIR__)->andReturn('');
////        $this->fs->shouldReceive('mirror')->times(10)->with()
//
//        $cache->mirror(['/src', '/tests']);
//    }
}
