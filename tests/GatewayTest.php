<?php

namespace Inmobile;


use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Inmobile\Exceptions\EmptyMessagePayload;
use Inmobile\Exceptions\GatewayError;
use PHPUnit\Framework\TestCase;

class GatewayTest extends TestCase
{
    protected Gateway $gateway;
    
    protected function setUp() : void 
    {
        parent::setUp();
        
        $this->gateway = Gateway::create('foo');
    }

    public function testGatewayCanHandleMessages()
    {
        $this->gateway->addMessage(
            Message::create('foo')->from('1245')->to(['4512345678', '4512345679'])
        );
        
        $this->assertCount(1, $this->gateway->getMessages());
    }

    public function testGatewayCanBuildXML()
    {
        $this->gateway->addMessage(
            Message::create('foo')
                ->from('1245')
                ->to([
                    (new Recipient('4512345679'))->withMessageId('123'), 
                    '4512345679'
                ])
        );

        $this->assertEquals(
            <<<XML
            <request><authentication apikey="foo"/><data><message><sendername>1245</sendername><text encoding="gsm7" flash="false"><![CDATA[foo]]></text><recipients><msisdn id="123">4512345679</msisdn><msisdn>4512345679</msisdn></recipients><respectblacklist>true</respectblacklist></message></data></request>
            XML,
            $this->gateway->toXml()
        );
    }
    
    public function testGatewayFailsIfNoMessagesPresent()
    {
        $this->expectException(EmptyMessagePayload::class);
        
        $this->gateway->send();
    }

    public function testGatewayFailsIfInmobileReturnsError()
    {
        $this->expectExceptionMessage('-1001');
        $this->expectException(GatewayError::class);
        
        $mockedResponse = new Response(200, [], '-1001');

        $mock = new MockHandler([$mockedResponse, $mockedResponse]);
        $mockHandler = new HandlerStack($mock);
        $client = new Client(['handler' => $mockHandler]);

        $gateway = new Gateway('foo', $client);
        
        $gateway->addMessage(
            Message::create('foo')
                ->from('1245')
                ->to([
                    (new Recipient('4512345679'))->withMessageId('123'),
                    '4512345679'
                ])
        );

        $gateway->send();
    }

    public function testGatewayCanSendMessages()
    {
        $stub = <<<XML
        <reply>
            <recipient msisdn="4512345679" id="123" />
            <recipient msisdn="4512345679" id="13cab0f4-0e4f-44cf-8f84-a9eb435f36a4" />
        </reply>
        XML;
        $mockedResponse = new Response(200, [], $stub);
        
        $mock = new MockHandler([$mockedResponse, $mockedResponse]);
        $mockHandler = new HandlerStack($mock);
        $client = new Client(['handler' => $mockHandler]);
        
        $gateway = new Gateway('foo', $client);
        
        $gateway->addMessage(
            Message::create('foo')
                ->from('1245')
                ->to([
                    (new Recipient('4512345679'))->withMessageId('123'),
                    '4512345679'
                ])
        );
        
        $response = $gateway->send();
        
        $this->assertEquals(200, $response->getResponse()->getStatusCode());
        $this->assertEquals($stub, $response->getResponse()->getBody()->getContents());
    }

    public function testGatewayCanReturnMultipleMessageIds()
    {
        $stub = <<<XML
        <reply>
            <recipient msisdn="4512345679" id="123" />
            <recipient msisdn="4512345679" id="13cab0f4-0e4f-44cf-8f84-a9eb435f36a4" />
        </reply>
        XML;
        $mockedResponse = new Response(200, [], $stub);

        $mock = new MockHandler([$mockedResponse, $mockedResponse]);
        $mockHandler = new HandlerStack($mock);
        $client = new Client(['handler' => $mockHandler]);

        $gateway = new Gateway('foo', $client);

        $gateway->addMessage(
            Message::create('foo')
                ->from('1245')
                ->to([
                    (new Recipient('4512345679'))->withMessageId('123'),
                    '4512345679'
                ])
        );

        $response = $gateway->send();

        $firstId = $response->id();
        $ids = $response->ids();
        
        $this->assertEquals('123', $firstId);
        $this->assertEquals([['4512345679' => '123'], ['4512345679' => '13cab0f4-0e4f-44cf-8f84-a9eb435f36a4']], $ids);
    }

    public function testGatewayCanReturnSingleMessageId()
    {
        $stub = <<<XML
        <reply>
            <recipient msisdn="4512345679" id="13cab0f4-0e4f-44cf-8f84-a9eb435f36a4" />
        </reply>
        XML;
        $mockedResponse = new Response(200, [], $stub);

        $mock = new MockHandler([$mockedResponse, $mockedResponse]);
        $mockHandler = new HandlerStack($mock);
        $client = new Client(['handler' => $mockHandler]);

        $gateway = new Gateway('foo', $client);

        $gateway->addMessage(
            Message::create('foo')
                ->from('1245')
                ->to([
                    new Recipient('4512345679')
                ])
        );

        $response = $gateway->send();

        $firstId = $response->id();

        $this->assertEquals('13cab0f4-0e4f-44cf-8f84-a9eb435f36a4', $firstId);
    }
}
