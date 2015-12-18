<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/18/15
 * Time: 1:52 AM
 */

namespace Epfremme\Everything\Tests\Process;

use Epfremme\Everything\Process\NullProcess;
use Symfony\Component\Process\Process;

class NullProcessTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $process = new NullProcess();

        $this->assertInstanceOf(Process::class, $process);
    }

    public function testStart()
    {
        $process = new NullProcess();

        $this->assertNull($process->start());
    }

    public function testRun()
    {
        $process = new NullProcess();

        $this->assertEquals(0, $process->run());
    }

    public function testWait()
    {
        $process = new NullProcess();

        $this->assertEmpty(0, $process->wait());
    }
}
