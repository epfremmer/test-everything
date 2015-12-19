<?php
/**
 * File CompleteProgressBarTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Output;

use Epfremme\Everything\Handler\Output\CompleteProgressBar;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CompleteProgressBarTest
 *
 * @package Epfremme\Everything\Tests\Handler\Output
 */
class CompleteProgressBarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OutputInterface|\Mockery\MockInterface
     */
    private $output;

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

        $this->output = \Mockery::mock(OutputInterface::class);
        $this->progressBar = \Mockery::mock(ProgressBar::class);
    }

    public function testConstruct()
    {
        $handler = new CompleteProgressBar($this->output, $this->progressBar);

        $this->assertAttributeSame($this->output, 'output', $handler);
        $this->assertAttributeSame($this->progressBar, 'progressBar', $handler);
    }

    public function testInvoke()
    {
        $handler = new CompleteProgressBar($this->output, $this->progressBar);

        $this->progressBar->shouldReceive('finish')->once()->withNoArgs();
        $this->output->shouldReceive('writeln')->once()->with('<info> [complete]</info>');

        $this->assertEquals('foo', $handler('foo'));
    }
}
