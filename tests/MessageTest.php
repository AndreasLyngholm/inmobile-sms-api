<?php

namespace Inmobile;


use Inmobile\Exceptions\InvalidSenderName;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{
    public function testCanCreateMessage()
    {
        $message = Message::create('foo');
        
        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('foo', $message->toArray()['text']['content']);
    }
    
    public function testCanAddSender()
    {
        $message = Message::create('foo')->from('bar');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('bar', $message->toArray()['sendername']);
    }

    public function testCanSetRecipientsByUsingToMethod()
    {
        $message = Message::create('foo')->from('bar')->to('451234567');

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('451234567', $message->toArray()['recipients'][0]['msisdn']);
        $this->assertCount(1, $message->toArray()['recipients']);

        $message = Message::create('foo')->from('bar')->to(['451234567', '451234565']);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('451234567', $message->toArray()['recipients'][0]['msisdn']);
        $this->assertEquals('451234565', $message->toArray()['recipients'][1]['msisdn']);
        $this->assertCount(2, $message->toArray()['recipients']);

        $message = Message::create('foo')->from('bar')->to([
            new Recipient('451234567'), 
            new Recipient('451234565')
        ]);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('451234567', $message->toArray()['recipients'][0]['msisdn']);
        $this->assertEquals('451234565', $message->toArray()['recipients'][1]['msisdn']);
        $this->assertCount(2, $message->toArray()['recipients']);
    }

    public function testCanSetRecipientsDirectly()
    {
        $message = Message::create('foo')->from('bar')->addRecipient(new Recipient('45123456'));

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals('45123456', $message->toArray()['recipients'][0]['msisdn']);
        $this->assertCount(1, $message->toArray()['recipients']);
    }
    
    public function testDoNotRespectBlacklist()
    {
        $message = Message::create('foo')
            ->from('bar')
            ->addRecipient(new Recipient('45123456'))
            ->doNotRespectBlacklist();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals(false, $message->toArray()['respectblacklist']);
    }

    public function testMessageCanBeScheduled()
    {
        $future = date_create('+5 days');
        
        $message = Message::create('foo')
            ->from('bar')
            ->addRecipient(new Recipient('45123456'))
            ->doNotRespectBlacklist()
            ->scheduleAt($future);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertEquals($future, $message->toArray()['sendtime']);
        
        $dom = new \DomDocument('1.0', 'UTF-8');
        
        $xml = $message->toXmlElement($dom);
        $this->assertEquals($future->format('Y-m-d H:i:s'), $xml->getElementsByTagName('sendtime')->item(0)->textContent);
    }

    public function testSenderNameCantBeLong()
    {
        $this->expectException(InvalidSenderName::class);

        Message::create('foo')->from('morethan11characters');
    }
}
