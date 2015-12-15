<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:56 AM
 */

namespace Epfremme\Everything\Handler;

use Symfony\Component\Console\Output\OutputInterface;

class WriteStepComplete
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * WriteStepComplete constructor
     *
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function __invoke()
    {
        $this->output->writeln(sprintf('<info> [complete]</info>'));

        return func_get_arg(0);
    }
}
