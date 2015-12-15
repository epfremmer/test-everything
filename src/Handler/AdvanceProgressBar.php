<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 11:42 PM
 */

namespace Epfremme\Everything\Handler;


use Symfony\Component\Console\Helper\ProgressBar;

class AdvanceProgressBar
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * AdvanceProgressBar constructor
     *
     * @param ProgressBar $progressBar
     */
    public function __construct(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
    }

    /**
     * Advance progress bar
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->progressBar->advance();

        return func_get_arg(0);
    }
}
