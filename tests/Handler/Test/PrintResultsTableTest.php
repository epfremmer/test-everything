<?php
/**
 * File PrintResultsTableTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Test;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\TestResult;
use Epfremme\Everything\Handler\Test\PrintResultsTable;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PrintResultsTableTest
 *
 * @package Epfremme\Everything\Tests\Handler\Test
 */
class PrintResultsTableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OutputInterface|\Mockery\MockInterface
     */
    private $output;

    /**
     * @var Collection
     */
    private $results;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->output = \Mockery::mock(OutputInterface::class);
        $this->results = new Collection([
            new TestResult(sha1('1'), 'a'),
            new TestResult(sha1('2'), 'b'),
            new TestResult(sha1('3'), 'c'),
        ]);
    }

    public function testConstruct()
    {
        $handler = new PrintResultsTable($this->results, $this->output);

        $this->assertAttributeSame($this->results, 'results', $handler);
        $this->assertAttributeSame($this->output, 'output', $handler);
    }

    public function testInvoke()
    {
        $handler = new PrintResultsTable($this->results, $this->output);
        $formatter = \Mockery::mock(OutputFormatterInterface::class);

        $this->output->shouldReceive('getFormatter')->andReturn($formatter);
        $this->output->shouldIgnoreMissing();
        $formatter->shouldIgnoreMissing();

        $this->assertEquals('foo', $handler('foo'));
    }
}
