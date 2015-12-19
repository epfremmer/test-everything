<?php
/**
 * File AdvanceProgressBarTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Output;

use Epfremme\Everything\Handler\Output\AdvanceProgressBar;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Class AdvanceProgressBarTest
 *
 * @package Epfremme\Everything\Tests\Handler\Output
 */
class AdvanceProgressBarTest extends \PHPUnit_Framework_TestCase
{
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

        $this->progressBar = \Mockery::mock(ProgressBar::class);
    }

    public function testConstruct()
    {
        $handler = new AdvanceProgressBar($this->progressBar);

        $this->assertAttributeEquals(1, 'steps', $handler);
        $this->assertAttributeSame($this->progressBar, 'progressBar', $handler);
    }

    public function testConstructWithSteps()
    {
        $handler = new AdvanceProgressBar($this->progressBar, 100);

        $this->assertAttributeEquals(100, 'steps', $handler);
        $this->assertAttributeSame($this->progressBar, 'progressBar', $handler);
    }

    public function testInvoke()
    {
        $handler = new AdvanceProgressBar($this->progressBar);

        $this->progressBar->shouldReceive('advance')->once()->with(1);

        $result = $handler('foo');

        $this->assertEquals('foo', $result);
    }

    public function testInvokeWithSteps()
    {
        $handler = new AdvanceProgressBar($this->progressBar, 100);

        $this->progressBar->shouldReceive('advance')->once()->with(100);

        $this->assertEquals('foo', $handler('foo'));
    }
}
