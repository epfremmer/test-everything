<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 11:31 PM
 */

namespace Epfremme\Everything\Handler\Test;


use Epfremme\Everything\Entity\TestResult;
use Epfremme\Everything\Parser\PHPUnitResultsParser;
use Symfony\Component\Process\Process;

class ParseTestResults
{
    /**
     * @var PHPUnitResultsParser
     */
    private $resultsParser;

    /**
     * ParseTestResults constructor
     *
     * @param PHPUnitResultsParser $resultsParser
     */
    public function __construct(PHPUnitResultsParser $resultsParser)
    {
        $this->resultsParser = $resultsParser;
    }

    /**
     * @param Process $process
     * @return TestResult
     */
    public function __invoke(Process $process)
    {
        $results = $process->getOutput();
        $result = $this->resultsParser->parse($results);
        $hash = $process->getWorkingDirectory()->getFilename();

        return new TestResult($hash, $result);
    }
}
