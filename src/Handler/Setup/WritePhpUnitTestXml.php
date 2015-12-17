<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/15/15
 * Time: 5:05 PM
 */

namespace Epfremme\Everything\Handler\Setup;


use Epfremme\Everything\FileSystem\Cache;
use Symfony\Component\Finder\SplFileInfo;

class WritePhpUnitTestXml
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    /**
     * WritePhpUnitTestXml constructor
     *
     * @param Cache $cache
     * @param \SimpleXMLElement $xml
     */
    public function __construct(Cache $cache, \SimpleXMLElement $xml)
    {
        $this->xml = $xml;
        $this->cache = $cache;
    }

    public function __invoke()
    {
        $this->cache->each(function(SplFileInfo $directory) {
            $this->xml->saveXML(sprintf('%s/phpunit.xml.dist', $directory));
        });

        return func_get_arg(0);
    }
}