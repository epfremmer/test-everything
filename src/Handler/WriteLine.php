<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 1:05 AM
 */

namespace Epfremme\Everything\Handler;


use Symfony\Component\Console\Output\OutputInterface;

class WriteLine
{
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var string
     */
    private $message;

    /**
     * WriteLine constructor
     *
     * @param OutputInterface $output
     * @param string $message
     */
    public function __construct(OutputInterface $output, $message = "")
    {
        $this->output = $output;
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        $this->output->writeLn($this->message);

        return func_get_arg(0);
    }
}
