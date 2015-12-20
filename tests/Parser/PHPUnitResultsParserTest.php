<?php
/**
 * File PHPUnitResultsParserTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Parser;

use Epfremme\Everything\Parser\PHPUnitResultsParser;

/**
 * Class PHPUnitResultsParserTest
 *
 * @package Epfremme\Everything\Tests\Parser
 */
class PHPUnitResultsParserTest extends \PHPUnit_Framework_TestCase
{
    const PHPUNIT_SUCCESS_OUTPUT = "PHPUnit 4.8.21 by Sebastian Bergmann and contributors.\n\n....\n\nTime: 63 ms, Memory: 5.50Mb\n\nOK (4 tests, 4 assertions)";
    const PHPUNIT_ERROR_OUTPUT = "PHPUnit 4.8.21 by Sebastian Bergmann and contributors.\n\n.E..\n\nTime: 69 ms, Memory: 5.50Mb\n\nThere was 1 error:\n\n1) Namespace\\Test::testStart\nException:\n\n/test/file.php:1\n\nFAILURES!\nTests: 4, Assertions: 3, Errors: 1.";

    public function testParseSuccess()
    {
        $parser = new PHPUnitResultsParser();

        $this->assertEquals('OK (4 tests, 4 assertions)', $parser->parse(self::PHPUNIT_SUCCESS_OUTPUT));
    }

    public function testParseFailure()
    {
        $parser = new PHPUnitResultsParser();

        $this->assertEquals('FAILURES! Tests: 4, Assertions: 3, Errors: 1.', $parser->parse(self::PHPUNIT_ERROR_OUTPUT));
    }

    /** @expectedException \InvalidArgumentException */
    public function testParseException()
    {
        $parser = new PHPUnitResultsParser();
        $parser->parse([]);
    }
}
