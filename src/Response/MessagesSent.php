<?php


namespace Inmobile\Response;


use Psr\Http\Message\ResponseInterface;

class MessagesSent
{
    protected ResponseInterface $response;
    protected ?\SimpleXMLElement $xml = null;
    
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
    
    public function getResponse() : ResponseInterface
    {
        return $this->response;
    }
    
    public function id() : string
    {
        return array_values($this->ids()[0])[0];
    }
    
    public function ids() : array
    {
        $this->consumeResponseAndLoadXML();
        
        $recipients = json_decode(json_encode($this->xml), true)['recipient'];
        
        return array_map(fn($r) => [$r['@attributes']['msisdn'] => $r['@attributes']['id']], $recipients);
    }
    
    private function consumeResponseAndLoadXML() : void
    {
        if(null === $this->xml) {
            $this->xml = simplexml_load_string($this->getResponse()->getBody()->getContents());
        }
    }
}