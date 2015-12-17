<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/16/15
 * Time: 12:10 AM
 */

namespace Epfremme\Everything\Entity;


class TestResult
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $result;

    /**
     * TestResult constructor
     *
     * @param string $hash
     * @param string $result
     */
    public function __construct($hash, $result)
    {
        $this->hash = $hash;
        $this->result = $result;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }
}
