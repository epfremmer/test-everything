<?php
/**
 * File CopyProjectFilesTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Setup;

use Epfremme\Everything\Composer\Json;
use Epfremme\Everything\Filesystem\Cache;
use Epfremme\Everything\Handler\Setup\CopyProjectFiles;
use Epfremme\Everything\Tests\Composer\JsonTest;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class CopyProjectFilesTest
 *
 * @package Epfremme\Everything\Tests\Handler\Setup
 */
class CopyProjectFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Json|\Mockery\MockInterface
     */
    private $json;

    /**
     * @var ProgressBar|\Mockery\MockInterface
     */
    private $progress;

    /**
     * @var Cache|\Mockery\MockInterface
     */
    private $cache;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->json = new Json(JsonTest::TEST_COMPOSER_JSON);
        $this->progress = \Mockery::mock(ProgressBar::class);
        $this->cache = \Mockery::mock(Cache::class);
    }

    public function testConstruct()
    {
        $handler = new CopyProjectFiles($this->cache, $this->json, $this->progress);

        $this->assertAttributeSame($this->cache, 'cache', $handler);
        $this->assertAttributeSame($this->json, 'json', $handler);
        $this->assertAttributeSame($this->progress, 'progress', $handler);
    }

    public function testInvoke()
    {
        $handler = new CopyProjectFiles($this->cache, $this->json, $this->progress);

        $this->cache->shouldReceive('mirror')->once()->with(['src/', 'tests/'], \Closure::class);

        $this->assertEquals('foo', $handler('foo'));
    }
}
