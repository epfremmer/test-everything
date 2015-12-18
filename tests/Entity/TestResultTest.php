<?php
/**
 * File TestResultTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Entity;

use Epfremme\Everything\Entity\TestResult;

/**
 * Class TestResultTest
 *
 * @package Epfremme\Everything\Tests\Entity
 */
class TestResultTest extends \PHPUnit_Framework_TestCase
{
    const TEST_RESULT_TEXT = "\nTest header\n\n...\n\nOK (3 tests, 3 assertions)";

    public function testConstruct()
    {
        $testResult = new TestResult(sha1('abc'), self::TEST_RESULT_TEXT);

        $this->assertInstanceOf(TestResult::class, $testResult);
        $this->assertAttributeEquals(sha1('abc'), 'hash', $testResult);
        $this->assertAttributeEquals(self::TEST_RESULT_TEXT, 'result', $testResult);
    }

    public function testGetHash()
    {
        $testResult = new TestResult(sha1('abc'), self::TEST_RESULT_TEXT);

        $this->assertEquals(sha1('abc'), $testResult->getHash());
    }

    public function testGetResult()
    {
        $testResult = new TestResult(sha1('abc'), self::TEST_RESULT_TEXT);

        $this->assertEquals(self::TEST_RESULT_TEXT, $testResult->getResult());
    }
}
