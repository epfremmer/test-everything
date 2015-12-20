<?php
/**
 * File ProcessorCounterTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Process;

use Epfremme\Everything\Process\ProcessorCounter;

/**
 * Class ProcessorCounterTest
 *
 * @package Epfremme\Everything\Tests\Process
 */
class ProcessorCounterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCpuCount()
    {
        $processorCounter = new ProcessorCounter();
        $processorCount = $processorCounter->getCpuCount();

        $this->assertInternalType('integer', $processorCount);
        $this->assertGreaterThan(0, $processorCount);
    }

    public function testToString()
    {
        $processorCounter = new ProcessorCounter();
        $processorCount = (string) $processorCounter;

        $this->assertInternalType('string', $processorCount);
        $this->assertGreaterThan(0, $processorCount);
    }
}
