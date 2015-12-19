<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:56 AM
 */

namespace Epfremme\Everything\Handler\Output;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class CompleteProgressBar
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * WriteStepComplete constructor
     *
     * @param OutputInterface $output
     * @param ProgressBar $progressBar
     */
    public function __construct(OutputInterface $output, ProgressBar $progressBar)
    {
        $this->output = $output;
        $this->progressBar = $progressBar;
    }

    /**
     * Complete progress bar
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->progressBar->finish();
        $this->output->writeln('<info> [complete]</info>');

        return func_get_arg(0);
    }
}
