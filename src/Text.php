<?php


namespace Inmobile;


use Inmobile\Exceptions\InvalidTextEncoding;

class Text
{
    const VALID_ENCODING_VALUES = ['gsm7', 'utf-8'];
    
    protected string $content;
    protected bool $flash = false;
    protected string $encoding = 'gsm7';
    
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return $this
     */
    public function flash() : self
    {
        $this->flash = true;
        
        return $this;
    }

    /**
     * @param string $encoding
     * @return $this
     */
    public function encoding(string $encoding) : self
    {
        if(!in_array($encoding, self::VALID_ENCODING_VALUES)) {
            throw new InvalidTextEncoding;    
        }
        
        $this->encoding = $encoding;
            
        return $this;
    }
    
    public function toArray()
    {
        return [
            'content' => $this->content,
            'encoding' => $this->encoding,
            'flash' => $this->flash
        ];
    }
    
    public function toXmlElement(\DOMDocument $dom) : \DOMElement
    {
        $element = $dom->createElement('text');
        
        $element->appendChild($dom->createCDATASection($this->content));
        $element->setAttribute('encoding', $this->encoding);
        $element->setAttribute('flash', $this->flash ? 'true' : 'false');
        
        return $element;
    }
}