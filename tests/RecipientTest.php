<?php

namespace Inmobile;


use Inmobile\Exceptions\InvalidMessageId;
use Inmobile\Exceptions\InvalidMsisdn;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{

    public function testCreateRecipient()
    {
        $recipient = Recipient::create('4512345678');
        
        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertEquals('4512345678', $recipient->toArray()['msisdn']);
    }
    
    public function testMessageIdCanBeSet()
    {
        $recipient = Recipient::create('4512345678')->withMessageId('123');

        $this->assertInstanceOf(Recipient::class, $recipient);
        $this->assertEquals('4512345678', $recipient->toArray()['msisdn']);
        $this->assertEquals('123', $recipient->toArray()['messageId']);
    }

    public function testMessageIdIsNotTooLong()
    {
        $this->expectException(InvalidMessageId::class);
        
        Recipient::create('4512345678')
            ->withMessageId('123456789123456789123456789123456789123456789123456789');
    }

    public function testMsisdnIsNotTooLong()
    {
        $this->expectException(InvalidMsisdn::class);

        Recipient::create('451234567845123456784512345678');
    }
}
