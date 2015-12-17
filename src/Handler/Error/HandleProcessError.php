<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 11:33 PM
 */

namespace Epfremme\Everything\Handler\Error;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\TestResult;
use Symfony\Component\Process\Process;

class HandleProcessError
{
    /**
     * @var Collection
     */
    private $results;

    /**
     * HandleProcessError constructor
     *
     * @param Collection $results
     */
    public function __construct(Collection $results)
    {
        $this->results = $results;
    }

    public function __invoke(Process $process)
    {
        $result = new TestResult($process->getWorkingDirectory()->getFilename(), 'ERROR');

        $this->results->set($result->getHash(), $result);
    }
}
