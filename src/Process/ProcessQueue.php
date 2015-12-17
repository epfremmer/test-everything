<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/16/15
 * Time: 11:45 AM
 */

namespace Epfremme\Everything\Process;

use Epfremme\Collection\Collection;
use GuzzleHttp\Promise\PromiseInterface;
use Symfony\Component\Process\Process;

class ProcessQueue extends Collection
{
    const SLEEP_MICRO_SECONDS = 1000;

    /**
     * @var int
     */
    private $limit;

    /**
     * ProcessQueue constructor.
     * @param array $elements
     * @param int $limit
     */
    public function __construct(array $elements = [], $limit = null)
    {
        parent::__construct($elements);

        $this->limit = $limit ?: (new ProcessorCounter())->getCpuCount();
    }

    /**
     * @return static
     */
    public function getPending()
    {
        return $this->filter(function(Process $process) {
            return !$process->isStarted();
        });
    }

    /**
     * @return static
     */
    public function getRunning()
    {
        return $this->filter(function(Process $process) {
            return $process->isRunning();
        });
    }

    /**
     * @return static
     */
    public function getCompleted()
    {
        return $this->filter(function(Process $process) {
            return $process->isTerminated();
        });
    }

    public function clearCompleted()
    {
        $this->getCompleted()->each(function(Process $process) {
            $process->wait();
            $this->resolve($process);
        });
    }

    public function resolve(Process $process)
    {
        $options = $process->getOptions();
        $promise = array_key_exists('promise', $options) ? $options['promise'] : null;

        if ($promise instanceof PromiseInterface) {
            $promise->wait();
        }

        $this->remove($process);
    }

    public function __invoke()
    {
        while (!$this->isEmpty()) {
            usleep(self::SLEEP_MICRO_SECONDS);

            $pending = $this->getPending();

            if ($pending->count() && $this->getRunning()->count() < $this->limit) {
                yield $pending->shift();
            } else {
                yield new NullProcess();
            }

            $this->clearCompleted();
        }
    }
}
