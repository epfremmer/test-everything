<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/16/15
 * Time: 12:05 AM
 */

namespace Epfremme\Everything\Handler\Test;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\TestResult;

class StoreTestResults
{
    /**
     * @var Collection
     */
    private $results;

    /**
     * StoreTestResults constructor
     *
     * @param Collection $results
     */
    public function __construct(Collection $results)
    {
        $this->results = $results;
    }

    public function __invoke(TestResult $result)
    {
        $this->results->set($result->getHash(), $result);
    }
}
