<?php
/**
 * File WriteTestConfigurationsTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Handler\Setup;

use Epfremme\Collection\Collection;
use Epfremme\Everything\Composer\Json;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Filesystem\Cache;
use Epfremme\Everything\Handler\Package\FilterPackageVersions;
use Epfremme\Everything\Handler\Setup\GetBaseConfiguration;
use Epfremme\Everything\Handler\Setup\WriteTestConfigurations;
use Epfremme\Everything\Tests\Composer\JsonTest;
use Epfremme\Everything\Tests\Entity\PackageTest;
use Epfremme\Everything\Tests\Traits\SerializerTrait;

/**
 * Class WriteTestConfigurationsTest
 *
 * @package Epfremme\Everything\Tests\Handler\Setup
 */
class WriteTestConfigurationsTest extends \PHPUnit_Framework_TestCase
{
    use SerializerTrait;

    /**
     * @var Collection
     */
    private $packages;

    /**
     * @var Cache|\Mockery\MockInterface
     */
    private $cache;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var \ArrayObject
     */
    private $base;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $serializer = $this->getSerializer();

        $this->packages = new Collection([
            'a/a' => $serializer->deserialize(str_replace('test/test', 'a/a', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
            'b/b' => $serializer->deserialize(str_replace('test/test', 'b/b', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
            'c/c' => $serializer->deserialize(str_replace('test/test', 'c/c', PackageTest::TEST_PACKAGE_JSON), Package::class, 'json'),
        ]);

        $baseConfigHandler = new GetBaseConfiguration($this->packages);
        $versionFilterHandler = new FilterPackageVersions('^1.0');

        $this->packages->each(function(Package $package) use ($versionFilterHandler) {
            $versionFilterHandler($package);
        });

        $this->cache = \Mockery::mock(Cache::class);
        $this->json = new Json(JsonTest::TEST_COMPOSER_JSON);
        $this->base = $baseConfigHandler();
    }

    public function testConstruct()
    {
        $handler = new WriteTestConfigurations($this->packages, $this->cache, $this->json);

        $this->assertAttributeSame($this->packages, 'packages', $handler);
        $this->assertAttributeSame($this->cache, 'cache', $handler);
        $this->assertAttributeSame($this->json, 'json', $handler);
    }

    public function testInvoke()
    {
        $handler = new WriteTestConfigurations($this->packages, $this->cache, $this->json);

        $this->cache->shouldReceive('addConfig')->with($this->json)->atMost()->times(2);

        $this->assertSame($this->packages, $handler($this->base));
    }
}
