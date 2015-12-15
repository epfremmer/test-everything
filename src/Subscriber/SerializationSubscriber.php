<?php
/**
 * Created by IntelliJ IDEA.
 * User: epfremme
 * Date: 12/14/15
 * Time: 8:46 PM
 */

namespace Epfremme\Everything\Subscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Epfremme\Everything\Annotation\Inline;
use JMS\Serializer\EventDispatcher\Events;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class SerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->reader = new AnnotationReader();
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ['event' => Events::PRE_DESERIALIZE, 'method' => 'onPreDeserialize'],
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $class = $event->getType()['name'];

        if (!class_exists($class)) {
            return; // skip custom JMS types
        }

        $data   = $event->getData();
        $object = new \ReflectionClass($class);
        $inline = $this->reader->getClassAnnotation($object, Inline::class);

        if ($inline) {
            $event->setData($data[$inline->getField()]);
        }
    }
}
