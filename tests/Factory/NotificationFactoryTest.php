<?php

namespace App\Tests\Factory;

use App\Entity\Notification;
use App\Entity\NotificationTemplate;
use App\Factory\NotificationFactory;
use PHPUnit\Framework\TestCase;

class NotificationFactoryTest extends TestCase
{
    private NotificationFactory $factory;
    private NotificationTemplate $template;

    protected function setUp(): void
    {
        $this->factory = new NotificationFactory();
        $this->template = new NotificationTemplate();
        
        // Setup a basic template
        $this->template
            ->setName('Test Template')
            ->setType('cmp_upd')
            ->setSubject('Test Subject')
            ->setContent('Test Content')
            ->setTriggerEvent('test.event')
            ->setActive(true);
    }

    public function testCreateFromTemplateWithValidData(): void
    {
        $recipient = 'test@example.com';
        $recipientType = 'email';
        $data = ['key' => 'value'];
        $sentVia = 'system';

        $notification = $this->factory->createFromTemplate(
            $this->template,
            $recipient,
            $recipientType,
            $data,
            $sentVia
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($this->template->getType(), $notification->getType());
        $this->assertEquals($this->template->getSubject(), $notification->getSubject());
        $this->assertEquals($this->template->getContent(), $notification->getBody());
        $this->assertEquals($recipient, $notification->getRecipient());
        $this->assertEquals($recipientType, $notification->getRecipientType());
        $this->assertEquals($data, $notification->getData());
        $this->assertEquals($sentVia, $notification->getSentVia());
    }

    public function testCreateFromTemplateWithNullOptionalParams(): void
    {
        $recipient = '+33612345678';
        $recipientType = 'phone';

        $notification = $this->factory->createFromTemplate(
            $this->template,
            $recipient,
            $recipientType
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($recipient, $notification->getRecipient());
        $this->assertEquals($recipientType, $notification->getRecipientType());
        $this->assertNull($notification->getData());
        $this->assertNull($notification->getSentVia());
    }

    public function testCreateFromTemplateWithNullContent(): void
    {
        $this->template->setContent(null);
        
        $notification = $this->factory->createFromTemplate(
            $this->template,
            'test@example.com',
            'email'
        );

        $this->assertEquals('', $notification->getBody());
    }

    public function testCreateFromTemplateWithInvalidRecipientType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->factory->createFromTemplate(
            $this->template,
            'test@example.com',
            'invalid_type'
        );
    }
}