<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 11:42 PM
 */

namespace Epfremme\Everything\Handler\Output;


use Symfony\Component\Console\Helper\ProgressBar;

class AdvanceProgressBar
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var int
     */
    private $steps;

    /**
     * AdvanceProgressBar constructor
     *
     * @param ProgressBar $progressBar
     * @param int $steps
     */
    public function __construct(ProgressBar $progressBar, $steps = 1)
    {
        $this->progressBar = $progressBar;
        $this->steps = $steps;
    }

    /**
     * Advance progress bar
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->progressBar->advance($this->steps);

        return func_get_arg(0);
    }
}
