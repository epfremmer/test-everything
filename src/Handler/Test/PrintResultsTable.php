<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/16/15
 * Time: 12:32 AM
 */

namespace Epfremme\Everything\Handler\Test;


use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\TestResult;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class PrintResultsTable
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Collection
     */
    private $results;

    /**
     * PrintResultsTable constructor
     *
     * @param Collection $results
     * @param OutputInterface $output
     */
    public function __construct(Collection $results, OutputInterface $output)
    {
        $this->output = $output;
        $this->results = $results;
    }

    public function __invoke()
    {
        $table = new Table($this->output);

        $table->setHeaders(['Hash', 'Result']);

        $this->results->each(function(TestResult $result) use ($table) {
            $table->addRow([
                $result->getHash(),
                $result->getResult() ?: 'ERROR',
            ]);
        });

        $table->render();

        return func_get_arg(0);
    }
}
