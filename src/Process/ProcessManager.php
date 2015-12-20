<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/16/15
 * Time: 11:58 AM
 */

namespace Epfremme\Everything\Process;


use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

class ProcessManager
{
    /**
     * @var ProcessQueue
     */
    private $queue;

    /**
     * @var ProcessFactory
     */
    private $factory;

    /**
     * ProcessFactory constructor
     *
     * @param ProcessFactory $factory
     */
    public function __construct(ProcessFactory $factory)
    {
        $this->queue = new ProcessQueue();

        $this->factory = $factory;
    }

    /**
     * @param SplFileInfo $directory
     * @return PromiseInterface
     */
    public function enqueue(SplFileInfo $directory)
    {
        $process = $this->factory->make($directory);

        /** @var Promise $promise */
        $promise = new Promise(function() use ($process, &$promise) {
            $process->wait();
            $promise->resolve($process);
        });

        $this->queue->add($process);

        $process->setOptions(['promise' => $promise]);

        return $promise;
    }

    public function run(\Closure $tick = null)
    {
        $queue = $this->queue;

        /** @var Process $next */
        foreach ($queue() as $next) {
            $next->start();
            $tick && $tick();
        }
    }
}
