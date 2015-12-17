<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 7:54 PM
 */

namespace Epfremme\Everything\Entity;

use Epfremme\Everything\Annotation\Inline;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Package
 *
 * @Inline("package")
 * @package Epfremme\Everything\Composer
 */
class Package
{
    /**
     * @JMS\Type("string")
     * @var string
     */
    private $name;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $description;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $time;

    /**
     * @JMS\Type("array")
     * @var array
     */
    private $maintainers;

    /**
     * @JMS\Type("array")
     * @var array
     */
    private $versions;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $type;

    /**
     * @JMS\Type("string")
     * @var string
     */
    private $repository;

    /**
     * @JMS\Type("array")
     * @var int[]
     */
    private $downloads;

    /**
     * @JMS\Type("integer")
     * @var int
     */
    private $favers;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function getMaintainers()
    {
        return $this->maintainers;
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return \int[]
     */
    public function getDownloads()
    {
        return $this->downloads;
    }

    /**
     * @return int
     */
    public function getFavers()
    {
        return $this->favers;
    }

    /**
     * @param array $versions
     * @return Package
     */
    public function setVersions($versions)
    {
        $this->versions = $versions;
        return $this;
    }
}
