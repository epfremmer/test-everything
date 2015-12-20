<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/20/15
 * Time: 2:25 AM
 */

namespace Epfremme\Everything\Process\Exception;

use Symfony\Component\Process\Process;

/**
 * Class InvalidProcessException
 *
 * @package Epfremme\Everything\Process\Exception
 */
class InvalidProcessException extends \InvalidArgumentException
{
    /**
     * InvalidProcessException constructor
     *
     * @param null|mixed $invalidProcess
     */
    public function __construct($invalidProcess = null)
    {
        $message = sprintf('Invalid class "%s" provided, expected %s', get_class($invalidProcess), Process::class);

        parent::__construct($message);
    }
}
