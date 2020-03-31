<?php

namespace Inmobile;

use Inmobile\Exceptions\InvalidSenderName;

class Message
{
    protected Text $text;
    protected ?string $sendername = null;
    protected array $recipients = [];
    protected bool $respectBlacklist = true;
    protected ?\DateTimeInterface $sendtime = null;

    public function __construct(Text $text)
    {
        $this->text = $text;
    }

    /**
     * @param string|Text $content
     * @return static
     */
    public static function create($content) : self
    {
        $text = $content instanceof Text ? $content : new Text($content);
            
        return new self($text);
    }

    /**
     * @param string $from
     * @return $this
     */
    public function from(string $from) : self
    {
        $this->setSenderName($from);
        
        return $this;
    }

    /**
     * @param string|string[]|Recipient|Recipient[] $recipients
     * @return $this
     */
    public function to($recipients) : self
    {
        if(!is_array($recipients)) {
            $recipients = [$recipients];
        }
        
        $recipients = array_map(function($recipient) {
            return $recipient instanceof Recipient ? $recipient : new Recipient($recipient);
        }, $recipients);
        
        $this->setRecipients($recipients);
        
        return $this;
    }

    public function addRecipient(Recipient $recipient) : Message
    {
        $this->recipients[] = $recipient;

        return $this;
    }
    
    public function doNotRespectBlacklist() : Message
    {
        $this->respectBlacklist = false;
        
        return $this;
    }
    
    public function scheduleAt(\DateTimeInterface $dateTime) : Message
    {
        $this->sendtime = $dateTime;
        
        return $this;
    }

    private function setRecipients(array $recipients) : Message
    {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        return $this;
    }

    private function setSenderName($sendername) : Message
    {
        if (strlen($sendername) > 11) {
            throw new InvalidSenderName;
        }

        $this->sendername = $sendername;

        return $this;
    }
    
    public function toArray()
    {
        return [
            'text' => $this->text->toArray(),
            'sendername' => $this->sendername,
            'recipients' => array_map(function(Recipient $recipient) {
                return $recipient->toArray();
            }, $this->recipients),
            'respectblacklist' => $this->respectBlacklist,
            'sendtime' => $this->sendtime
        ];
    }
    
    public function toXmlElement(\DOMDocument $dom) : \DOMElement
    {
        $element = $dom->createElement('message');
        
        $element->appendChild($dom->createElement('sendername', $this->sendername));
        $element->appendChild($this->text->toXmlElement($dom));
        
        $recipients = $dom->createElement('recipients');
        
        foreach($this->recipients as $recipient) {
            $recipients->appendChild($recipient->toXmlElement($dom));
        }
        
        $respectBlacklist = $dom->createElement('respectblacklist', $this->respectBlacklist ? 'true' : 'false');
        
        if($this->sendtime) {
            $sendtime = $dom->createElement('sendtime', $this->sendtime->format('Y-m-d H:i:s'));
            $element->appendChild($sendtime);
        }
        
        $element->appendChild($recipients);
        $element->appendChild($respectBlacklist);
        
        return $element;
    }
}
