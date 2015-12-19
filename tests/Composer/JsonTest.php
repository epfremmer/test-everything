<?php
/**
 * File JsonTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Composer;

use Epfremme\Everything\Composer\Json;

/**
 * Class JsonTest
 *
 * @package Epfremme\Everything\Tests\Composer
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    const TEST_COMPOSER_JSON = '{"name":"test/test","description":"test description","license":"MIT","type":"library","authors":[{"name":"epfremme","email":"epfremme@nerdery.com"}],"autoload":{"psr-4":{"Epfremme\\\\":"src/","Epfremme\\\\Tests\\\\":"tests/"}},"require":{"php":">=5.6","composer/composer":"^1.0"},"require-dev":{"phpunit/phpunit":"~5.0"},"config":{"bin-dir":"bin/"}}';

    public function testConstruct()
    {
        $json = new Json();

        $this->assertAttributeEquals(Json::DEFAULT_JSON, 'json', $json);
        $this->assertAttributeEquals(json_decode(Json::DEFAULT_JSON, true), 'data', $json);
    }

    public function testConstructWithArgs()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);

        $this->assertAttributeEquals(self::TEST_COMPOSER_JSON, 'json', $json);
        $this->assertAttributeEquals(json_decode(self::TEST_COMPOSER_JSON, true), 'data', $json);
    }

    public function testGetRequire()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);
        $expected = [
            'php' => '>=5.6',
            'composer/composer' => '^1.0',
        ];

        $this->assertEquals($expected, $json->getRequire());
    }

    public function testGetRequireMissing()
    {
        $json = new Json();

        $this->assertEquals([], $json->getRequire());
    }

    public function testSetRequire()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);
        $json->setRequire([]);

        $this->assertEquals([], $json->getRequire());
    }

    public function testGetRequireDev()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);
        $expected = [
            'phpunit/phpunit' => '~5.0',
        ];

        $this->assertEquals($expected, $json->getRequireDev());
    }

    public function testGetRequireDevMissing()
    {
        $json = new Json();

        $this->assertEquals([], $json->getRequireDev());
    }

    public function testSetRequireDev()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);
        $json->setRequireDev([]);

        $this->assertEquals([], $json->getRequireDev());
    }

    public function testGet()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);

        $this->assertEquals('test/test', $json->get('name'));
        $this->assertSame($json->getRequire(), $json->get('require'));
        $this->assertNull($json->get('noop'));
    }

    public function testToJson()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);

        $this->assertJsonStringEqualsJsonString(self::TEST_COMPOSER_JSON, $json->toJson());
    }

    public function testToString()
    {
        $json = new Json(self::TEST_COMPOSER_JSON);

        $this->assertJsonStringEqualsJsonString(self::TEST_COMPOSER_JSON, (string) $json);
    }
}
