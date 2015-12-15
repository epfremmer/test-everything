<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 7:54 PM
 */

namespace Epfremme\Everything\Handler;

use Epfremme\Everything\Composer\Package;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

class DeserializePackage
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * DeserializePackage constructor
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param ResponseInterface $response
     * @return Package
     */
    public function __invoke(ResponseInterface $response)
    {
        return $this->serializer->deserialize($response->getBody(), Package::class, 'json');
    }
}
