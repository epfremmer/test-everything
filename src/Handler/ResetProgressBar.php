<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:44 AM
 */

namespace Epfremme\Everything\Handler;

use Symfony\Component\Console\Helper\ProgressBar;

class ResetProgressBar
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * ResetProgressBar constructor
     *
     * @param ProgressBar $progressBar
     */
    public function __construct(ProgressBar $progressBar)
    {
        $this->progressBar = $progressBar;
    }

    public function __invoke($count)
    {
        $this->progressBar->start($count);
    }
}
