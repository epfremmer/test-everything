<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/19/15
 * Time: 6:04 PM
 */

namespace Epfremme\Everything\Tests\Traits;


use Epfremme\Everything\Subscriber\SerializationSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

trait SerializerTrait
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var SerializerBuilder
     */
    private static $serializerBuilder;

    /**
     * @return Serializer
     */
    public function getSerializer()
    {
        if (!$this->serializer) {
            $this->serializer = $this->getSerializerBuilder()->build();
        }

        return $this->serializer;
    }

    /**
     * @return SerializerBuilder
     */
    public static function getSerializerBuilder()
    {
        if (!self::$serializerBuilder) {
            self::$serializerBuilder = new SerializerBuilder();

            self::$serializerBuilder->configureListeners(function(EventDispatcher $eventDispatcher) {
                $eventDispatcher->addSubscriber(new SerializationSubscriber());
            });
        }

        return self::$serializerBuilder;
    }
}
