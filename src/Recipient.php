<?php

namespace Inmobile;

use Inmobile\Exceptions\InvalidMessageId;
use Inmobile\Exceptions\InvalidMsisdn;

class Recipient
{
    protected string $msisdn;
    protected ?string $messageId = null;

    public function __construct(string $msisdn)
    {
        $this->setMsisdn($msisdn);
    }
    
    public static function create(string $msisdn) : self
    {
        return new self($msisdn);
    }

    public function withMessageId(string $messageId) : self
    {
        if(strlen($messageId) > 50) {
            throw new InvalidMessageId;    
        }
        
        $this->messageId = $messageId;
        
        return $this;
    }

    private function setMsisdn(string $msisdn)
    {
        if(strlen($msisdn) > 20) {
            throw new InvalidMsisdn;
        }

        $this->msisdn = $msisdn;
    }
    
    public function toArray()
    {
        return [
            'msisdn' => $this->msisdn,
            'messageId' => $this->messageId
        ];
    }

    public function toXmlElement(\DOMDocument $dom) : \DOMElement
    {
        $element = $dom->createElement('msisdn', $this->msisdn);
        
        if($this->messageId) {
            $element->setAttribute('id', $this->messageId);
        }

        return $element;
    }
}
