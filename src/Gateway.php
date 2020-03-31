<?php

namespace Inmobile;

use GuzzleHttp\Client;
use Inmobile\Exceptions\ResponseExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Inmobile\Exceptions\EmptyMessagePayload;

class Gateway
{
    const ENDPOINT = 'https://mm.inmobile.dk';

    protected array $messages = [];
    protected string $apiKey;
    protected Client $client;
    protected ResponseExceptionHandler $responseExceptionHandler; 

    public function __construct($apiKey, Client $client)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
        $this->responseExceptionHandler = new ResponseExceptionHandler();
    }
    
    public static function create(string $apiKey) : self
    {
        return new self($apiKey, new Client(['base_uri' => self::ENDPOINT]));
    }

    public function addMessage(Message $message) : Gateway
    {
        $this->messages[] = $message;

        return $this;
    }
    
    public function getMessages()
    {
        return $this->messages;
    }
    
    public function toXml()
    {
        $dom = new \DomDocument('1.0', 'UTF-8');
        
        $request = $dom->createElement('request');
        $data = $dom->createElement('data');
        
        foreach($this->messages as $message) {
            $messageElement = $message->toXmlElement($dom);
            $data->appendChild($messageElement);
        }
        
        $authentication = $dom->createElement('authentication');
        $authentication->setAttribute('apikey', $this->apiKey);

        $request->appendChild($authentication);
        $request->appendChild($data);
        $dom->appendChild($request);

        return $dom->saveXML($dom->documentElement);
    }

    public function send(): ResponseInterface
    {
        if (count($this->messages) < 1) {
            throw new EmptyMessagePayload;
        }
        
        $response = $this->client->post('/Api/V2/SendMessages', [ 'form_params' => [ 'xml' => $this->toXml() ] ] );
        
        return $this->responseExceptionHandler->transformResponseToException($response);
    }
}
