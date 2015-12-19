<?php
/**
 * File HandleProcessErrorTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Error;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\TestResult;
use Epfremme\Everything\Handler\Error\HandleProcessError;
use Symfony\Component\Process\Process;

/**
 * Class HandleProcessErrorTest
 *
 * @package Epfremme\Everything\Tests\Handler\Error
 */
class HandleProcessErrorTest extends \PHPUnit_Framework_TestCase
{
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

        $this->results = new Collection();
    }

    public function testConstruct()
    {
        $handler = new HandleProcessError($this->results);

        $this->assertAttributeSame($this->results, 'results', $handler);
    }

    public function testInvoke()
    {
        $handler = new HandleProcessError($this->results);
        $process = \Mockery::mock(Process::class);

        $process->shouldReceive('getWorkingDirectory->getFilename')->once()->andReturn(sha1(''));
        $process->shouldIgnoreMissing();

        /** @var TestResult $result */
        $result = $handler($process);

        $this->assertInstanceOf(TestResult::class, $result);
        $this->assertEquals($result->getResult(), 'ERROR');
        $this->assertEquals($result->getHash(), sha1(''));
        $this->assertCount(1, $this->results);
        $this->assertContainsOnlyInstancesOf(TestResult::class, $this->results);
    }
}
