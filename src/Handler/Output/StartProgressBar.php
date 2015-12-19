<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 12:44 AM
 */

namespace Epfremme\Everything\Handler\Output;

use Symfony\Component\Console\Helper\ProgressBar;

class StartProgressBar
{
    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var \Countable
     */
    private $countable;

    /**
     * ResetProgressBar constructor
     *
     * @param ProgressBar $progressBar
     * @param \Countable $countable
     */
    public function __construct(ProgressBar $progressBar, \Countable $countable = null)
    {
        $this->progressBar = $progressBar;
        $this->countable = $countable;
    }

    /**
     * Start/Restart progress bar with max if countable
     * provided during construct
     *
     * @return mixed
     */
    public function __invoke()
    {
        $max = $this->countable ? $this->countable->count() : null;

        $this->progressBar->start($max);

        return func_get_arg(0);
    }
}
