<?php
/**
 * File StoreTestResultsTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Test;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Entity\TestResult;
use Epfremme\Everything\Handler\Test\StoreTestResults;

/**
 * Class StoreTestResultsTest
 *
 * @package Epfremme\Everything\Tests\Handler\Test
 */
class StoreTestResultsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    private $results;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->results = new Collection();
    }

    public function testConstruct()
    {
        $handler = new StoreTestResults($this->results);

        $this->assertAttributeSame($this->results, 'results', $handler);
    }

    public function testInvoke()
    {
        $handler = new StoreTestResults($this->results);
        $result = new TestResult(sha1('1'), 'a');

        $this->assertSame($result, $handler($result));
        $this->assertSame($result, $this->results->get(sha1('1')));
        $this->assertCount(1, $this->results);
    }
}
