<?php
/**
 * File WriteLineTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Output;

use Epfremme\Everything\Handler\Output\WriteLine;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class WriteLineTest
 *
 * @package Epfremme\Everything\Tests\Handler\Output
 */
class WriteLineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OutputInterface|\Mockery\MockInterface
     */
    private $output;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->output = \Mockery::mock(OutputInterface::class);
    }

    public function testConstruct()
    {
        $handler = new WriteLine($this->output);

        $this->assertAttributeSame($this->output, 'output', $handler);
        $this->assertAttributeEquals('', 'message', $handler);
    }

    public function testConstructWithMessage()
    {
        $handler = new WriteLine($this->output, 'output message');

        $this->assertAttributeSame($this->output, 'output', $handler);
        $this->assertAttributeEquals('output message', 'message', $handler);
    }

    public function testInvoke()
    {
        $handler = new WriteLine($this->output);

        $this->output->shouldReceive('writeln')->once()->with('');

        $this->assertEquals('foo', $handler('foo'));
    }

    public function testInvokeWithMessage()
    {
        $handler = new WriteLine($this->output, 'test message');

        $this->output->shouldReceive('writeln')->once()->with('test message');

        $this->assertEquals('foo', $handler('foo'));
    }
}
