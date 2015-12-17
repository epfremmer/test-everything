<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 6:02 PM
 */

namespace Epfremme\Everything\Process;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Process\Process;

class ProcessFactory
{
    /**
     * @var string
     */
    private $cmd;

    /**
     * ProcessFactory constructor
     *
     * @param string $cmd
     */
    public function __construct($cmd)
    {
        $this->cmd = $cmd;
    }

    /**
     * @param SplFileInfo $directory
     * @return Process
     */
    public function make(SplFileInfo $directory)
    {
        return new Process($this->cmd, $directory);
    }
}
