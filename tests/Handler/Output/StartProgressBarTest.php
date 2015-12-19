<?php
/**
 * File StartProgressBarTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Output;

use Epfremme\Everything\Handler\Output\StartProgressBar;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class StartProgressBarTest
 *
 * @package Epfremme\Everything\Tests\Handler\Output
 */
class StartProgressBarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Countable|\Mockery\MockInterface
     */
    private $countable;

    /**
     * @var ProgressBar|\Mockery\MockInterface
     */
    private $progressBar;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->countable = \Mockery::mock(\Countable::class);
        $this->progressBar = \Mockery::mock(ProgressBar::class);
    }

    public function testConstruct()
    {
        $handler = new StartProgressBar($this->progressBar);

        $this->assertAttributeSame($this->progressBar, 'progressBar', $handler);
        $this->assertAttributeEquals(null, 'countable', $handler);
    }

    public function testConstructWithCountable()
    {
        $handler = new StartProgressBar($this->progressBar, $this->countable);

        $this->assertAttributeSame($this->progressBar, 'progressBar', $handler);
        $this->assertAttributeSame($this->countable, 'countable', $handler);
    }

    public function testInvoke()
    {
        $handler = new StartProgressBar($this->progressBar);

        $this->progressBar->shouldReceive('start')->once()->with(null);

        $this->assertEquals('foo', $handler('foo'));
    }

    public function testInvokeWithCountable()
    {
        $handler = new StartProgressBar($this->progressBar, $this->countable);

        $this->countable->shouldReceive('count')->once()->andReturn(100);
        $this->progressBar->shouldReceive('start')->once()->with(100);

        $this->assertEquals('foo', $handler('foo'));
    }
}
