<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 8:09 PM
 */

namespace Epfremme\Everything\Service;

use GuzzleHttp\Client;
use Tebru\Retrofit\Annotation as Rest;

class PackagistService
{
    /**
     * PackagistService constructor
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://packagist.org',
        ]);
    }

    /**
     * @param string $package
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function getPackage($package)
    {
        return $this->client->getAsync(sprintf('/packages/%s.json', $package));
    }
}
