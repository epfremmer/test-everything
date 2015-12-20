<?php
/**
 * File ProcessQueueTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Process;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Process\ProcessorCounter;
use Epfremme\Everything\Process\ProcessQueue;
use GuzzleHttp\Promise\Promise;
use Symfony\Component\Process\Process;

/**
 * Class ProcessQueueTest
 *
 * @package Epfremme\Everything\Tests\Process
 */
class ProcessQueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Add optional promise to process options
     *
     * @param Process $process
     * @return Promise
     */
    private function addPromise(Process $process)
    {
        /** @var Promise $promise */
        $promise = new Promise(function() use ($process, &$promise) {
            $process->wait();
            $promise->resolve($process);
        });

        $process->setOptions([ProcessQueue::PROMISE_KEY => $promise]);

        return $promise;
    }

    public function testConstruct()
    {
        $queue = new ProcessQueue();
        $counter = new ProcessorCounter();

        $this->assertInstanceOf(Collection::class, $queue);
        $this->assertAttributeEquals($counter->getCpuCount(), 'limit', $queue);
    }

    public function testConstructWithArgs()
    {
        $queue = new ProcessQueue([], 4);

        $this->assertInstanceOf(Collection::class, $queue);
        $this->assertAttributeEquals(4, 'limit', $queue);
    }

    /** @expectedException \Epfremme\Everything\Process\Exception\InvalidProcessException */
    public function testConstructException()
    {
        new ProcessQueue([new \ArrayObject()]);
    }

    public function testGetPending()
    {
        $queue = new ProcessQueue();
        $process = new Process('pwd');

        $queue->add($process);

        $pending = $queue->getPending();

        $this->assertInstanceOf(ProcessQueue::class, $pending);
        $this->assertContainsOnly(Process::class, $pending);
        $this->assertInstanceOf(Process::class, $pending->get(0));
        $this->assertSame($process, $pending->get(0));
    }

    public function testGetRunning()
    {
        $queue = new ProcessQueue();
        $process = new Process('pwd');

        $queue->add($process);

        $this->assertEmpty($queue->getRunning());

        $process->start();

        $running = $queue->getRunning();

        $this->assertInstanceOf(ProcessQueue::class, $running);
        $this->assertContainsOnly(Process::class, $running);
        $this->assertInstanceOf(Process::class, $running->get(0));
        $this->assertSame($process, $running->get(0));
    }

    public function testGetCompleted()
    {
        $queue = new ProcessQueue();
        $process = new Process('pwd');

        $queue->add($process);

        $this->assertEmpty($queue->getCompleted());

        $process->run();

        $completed = $queue->getCompleted();

        $this->assertInstanceOf(ProcessQueue::class, $completed);
        $this->assertContainsOnly(Process::class, $completed);
        $this->assertInstanceOf(Process::class, $completed->get(0));
        $this->assertSame($process, $completed->get(0));
    }

    public function testClearCompleted()
    {
        $queue = new ProcessQueue();
        $process = new Process('pwd');

        $queue->add($process);
        $process->run();

        $queue->clearCompleted();

        $this->assertEmpty($queue);
        $this->assertCount(0, $queue);
    }

    public function testResolve()
    {
        $queue = new ProcessQueue();
        $process = new Process('pwd');

        $queue->add($process);
        $process->run();
        $queue->resolve($process);

        $this->assertEmpty($queue);
        $this->assertCount(0, $queue);
        $this->assertTrue($process->isTerminated());
    }

    public function testResolveWithPromise()
    {
        $queue = new ProcessQueue();
        $process = new Process('pwd');
        $promise = $this->addPromise($process);

        $isResolved = false;
        $promise->then(function() use (&$isResolved) {
            $isResolved = true;
        });

        $this->assertFalse($isResolved);

        $queue->add($process);
        $process->run();
        $queue->resolve($process);

        $this->assertEmpty($queue);
        $this->assertCount(0, $queue);
        $this->assertTrue($isResolved);
        $this->assertTrue($process->isTerminated());
    }

    public function testInvoke()
    {
        $queue = new ProcessQueue();
        $process = new Process('pwd');

        $queue->add($process);

        /** @var Process $pending */
        foreach ($queue() as $pending) {
            $this->assertInstanceOf(Process::class, $pending);
            $this->assertFalse($pending->isStarted());

            $pending->start();
        }

        $this->assertEmpty($queue);
        $this->assertCount(0, $queue);
        $this->assertTrue($process->isTerminated());
    }
}
