<?php
/**
 * File ApplicationTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests;

use Epfremme\Everything\Application;

/**
 * Class ApplicationTest
 *
 * @package Epfremme\Everything\Tests
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $application = new Application();

        $this->assertAttributeEquals('everything', 'name', $application);
        $this->assertAttributeEquals('1.0.0', 'version', $application);
    }
}
