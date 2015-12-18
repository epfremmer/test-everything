<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/16/15
 * Time: 5:43 PM
 */

namespace Epfremme\Everything\Process;


use Symfony\Component\Process\Process;

class NullProcess extends Process
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function start($callback = null)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function run($callback = null)
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function wait($callback = null)
    {
        return 0;
    }
}
