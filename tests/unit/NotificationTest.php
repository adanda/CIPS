<?php

require_once __DIR__.'/../../src/Cips/Notifications/Notification.php';

/**
 * Unit Tests for the Notification Class
 *
 * @author Alfred Danda
 */
class NotificationTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $notification =
            $this->getMockForAbstractClass('Cips\Notifications\Notification');
        $notification->expects($this->any())
            ->method('notify')
            ->will($this->returnValue(null));

        $notification->__construct();
        $this->assertFalse($notification->isAlwaysSend(),
            '__construct() sets alwaysSend to false when no value is given');
        $notification->__construct(true);
        $this->assertTrue($notification->isAlwaysSend(),
            '__construct() sets alwaysSend to true when true is given');
        $notification->__construct(false);
        $this->assertFalse($notification->isAlwaysSend(),
            '__construct() sets alwaysSend to false when false is given');
    }

    public function testSetAlwaysSend()
    {
        $notification =
            $this->getMockForAbstractClass('Cips\Notifications\Notification');
        $notification->expects($this->any())
            ->method('notify')
            ->will($this->returnValue(null));

        $notification->setAlwaysSend(true);
        $this->assertTrue($notification->isAlwaysSend(),
            'setAlwaysSend() sets the property to true when true is given');
        $this->assertInstanceOf('Cips\Notifications\Notification',
            $notification->setAlwaysSend(false),
            'setAlwaysSend() returns an Notification object');
        $this->assertFalse($notification->isAlwaysSend(),
            'setAlwaysSend() sets the property to false when false is given');
    }

    public function testisAlwaysSend()
    {
        $notification =
            $this->getMockForAbstractClass('Cips\Notifications\Notification');
        $notification->expects($this->any())
            ->method('notify')
            ->will($this->returnValue(null));

        $notification->isAlwaysSend(true);
        $this->assertFalse($notification->isAlwaysSend(),
            'isAlwaysSend() returns initially false');
    }
}