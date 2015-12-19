<?php
/**
 * File Json.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Composer;

/**
 * Class Json
 *
 * @package Epfremme\Everything\Composer
 */
class Json
{
    const DEFAULT_JSON = '{}';
    const REQUIRE_KEY = 'require';
    const REQUIRE_DEV_KEY = 'require-dev';

    /**
     * @var string
     */
    private $json;

    /**
     * @var array
     */
    private $data;

    /**
     * Composer Json constructor
     *
     * @param string $json - Defaults to empty array
     */
    public function __construct($json = self::DEFAULT_JSON)
    {
        $this->json = $json;
        $this->data = (array) json_decode($json, true);
    }

    /**
     * Return require array
     *
     * @return array
     */
    public function getRequire()
    {
        return array_key_exists(self::REQUIRE_KEY, $this->data) ? $this->data[self::REQUIRE_KEY] : [];
    }

    /**
     * Set require array
     *
     * @param array $packages
     * @return $this
     */
    public function setRequire(array $packages)
    {
        $this->data[self::REQUIRE_KEY] = $packages;

        return $this;
    }

    /**
     * Return require-dev array
     *
     * @return array
     */
    public function getRequireDev()
    {
        return array_key_exists(self::REQUIRE_DEV_KEY, $this->data) ? $this->data[self::REQUIRE_DEV_KEY] : [];
    }

    /**
     * Set require-dev array
     *
     * @param array $packages
     * @return $this
     */
    public function setRequireDev(array $packages)
    {
        $this->data[self::REQUIRE_DEV_KEY] = $packages;

        return $this;
    }

    /**
     * Return value or null at target key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->data)) {
            return null;
        }

        return $this->data[$key];
    }

    /**
     * Return current data as JSON
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }

    /**
     * Cast data to JSON string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
