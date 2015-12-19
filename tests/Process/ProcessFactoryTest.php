<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/18/15
 * Time: 1:52 AM
 */

namespace Epfremme\Everything\Tests\Process;

use Epfremme\Everything\Process\ProcessFactory;
use Mockery;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

class ProcessFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $factory = new ProcessFactory('pwd');

        $this->assertAttributeEquals('pwd', 'cmd', $factory);
    }

    public function testMake()
    {
        $factory = new ProcessFactory('pwd');

        /** @var SplFileInfo $fileinfo */
        $fileinfo = Mockery::mock(SplFileInfo::class);
        $process = $factory->make($fileinfo);

        $this->assertInstanceOf(Process::class, $process);
        $this->assertEquals('pwd', $process->getCommandLine());
        $this->assertSame($fileinfo, $process->getWorkingDirectory());
        $this->assertFalse($process->isStarted());
        $this->assertFalse($process->isRunning());
    }
}
