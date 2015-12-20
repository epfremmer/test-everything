<?php
/**
 * File SerializationSubscriberTest.php
 *
 * @author Edward Pfremmer <epfremme@nerdery.com>
 */
namespace Epfremme\Everything\Tests\Subscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Epfremme\Everything\Entity\Package;
use Epfremme\Everything\Subscriber\SerializationSubscriber;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

/**
 * Class SerializationSubscriberTest
 *
 * @package Epfremme\Everything\Tests\Subscriber
 */
class SerializationSubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $subscriber = new SerializationSubscriber();

        $this->assertAttributeInstanceOf(AnnotationReader::class, 'reader', $subscriber);
    }

    public function testGetSubscribedEvents()
    {
        $subscriber = new SerializationSubscriber();

        $this->assertInternalType('array', $subscriber->getSubscribedEvents());
        $this->assertContainsOnly('array', $subscriber->getSubscribedEvents());

        foreach ($subscriber->getSubscribedEvents() as $event) {
            $this->assertArrayHasKey('event', $event);
            $this->assertArrayHasKey('method', $event);
            $this->assertTrue(method_exists($subscriber, $event['method']));
        }
    }

    public function onPreDeserialize()
    {
        $subscriber = new SerializationSubscriber();
        $event = \Mockery::mock(PreDeserializeEvent::class);

        $event->shouldReceive('getType')->once()->withNoArgs()->andReturn(['name' => Package::class]);
        $event->shouldReceive('getData')->once()->withNoArgs()->andReturn(['package' => ['name' => 'test/test']]);
        $event->shouldReceive('setData')->once()->with(['name' => 'test/test'])->andReturn();

        $subscriber->onPreDeserialize($event);
    }
}
