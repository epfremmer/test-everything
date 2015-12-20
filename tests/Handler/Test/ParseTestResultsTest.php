<?php
/**
 * File ParseTestResultsTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Test;

use Epfremme\Everything\Entity\TestResult;
use Epfremme\Everything\Handler\Test\ParseTestResults;
use Epfremme\Everything\Parser\PHPUnitResultsParser;
use Symfony\Component\Process\Process;

/**
 * Class ParseTestResultsTest
 *
 * @package Epfremme\Everything\Tests\Handler\Test
 */
class ParseTestResultsTest extends \PHPUnit_Framework_TestCase
{
    const PHPUNIT_SUCCESS_OUTPUT = "PHPUnit 4.8.21 by Sebastian Bergmann and contributors.\n\n....\n\nTime: 63 ms, Memory: 5.50Mb\n\nOK (4 tests, 4 assertions)";
    const PHPUNIT_ERROR_OUTPUT = "PHPUnit 4.8.21 by Sebastian Bergmann and contributors.\n\n.E..\n\nTime: 69 ms, Memory: 5.50Mb\n\nThere was 1 error:\n\n1) Namespace\\Test::testStart\nException:\n\n/test/file.php:1\n\nFAILURES!\nTests: 4, Assertions: 3, Errors: 1.";

    /**
     * @var PHPUnitResultsParser
     */
    private $parser;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new PHPUnitResultsParser();
    }

    public function testConstruct()
    {
        $handler = new ParseTestResults($this->parser);

        $this->assertAttributeSame($this->parser, 'resultsParser', $handler);
    }

    public function testInvokeOnSuccess()
    {
        $handler = new ParseTestResults($this->parser);
        $process = \Mockery::mock(Process::class);

        $process->shouldReceive('getOutput')->once()->withNoArgs()->andReturn(self::PHPUNIT_SUCCESS_OUTPUT);
        $process->shouldReceive('getWorkingDirectory->getFilename')->once()->withNoArgs()->andReturn(sha1(''));
        $process->shouldIgnoreMissing();

        /** @var TestResult $result */
        $result = $handler($process);

        $this->assertInstanceOf(TestResult::class, $result);
        $this->assertEquals($result->getHash(), sha1(''));
        $this->assertEquals('OK (4 tests, 4 assertions)', $result->getResult());
    }

    public function testInvokeOnFailure()
    {
        $handler = new ParseTestResults($this->parser);
        $process = \Mockery::mock(Process::class);

        $process->shouldReceive('getOutput')->once()->withNoArgs()->andReturn(self::PHPUNIT_ERROR_OUTPUT);
        $process->shouldReceive('getWorkingDirectory->getFilename')->once()->withNoArgs()->andReturn(sha1(''));
        $process->shouldIgnoreMissing();

        /** @var TestResult $result */
        $result = $handler($process);

        $this->assertInstanceOf(TestResult::class, $result);
        $this->assertEquals($result->getHash(), sha1(''));
        $this->assertEquals('Tests: 4, Assertions: 3, Errors: 1.', $result->getResult());
    }
}
