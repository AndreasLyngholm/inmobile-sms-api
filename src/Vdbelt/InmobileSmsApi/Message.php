<?php namespace Vdbelt\InmobileSmsApi;

/**
 * Class Message represents an object holding the message data
 * @package Vdbelt\InmobileSmsApi
 */
class Message
{

    /**
     * The content of the message
     * @var string
     */
    protected $content 		= '';

    /**
     * The sender name of the message
     * @var string
     */
    protected $sendername 	= '';

    /**
     * An array with msisdns containing the recipients
     * @var array
     */
    protected $recipients 	= array();

    /**
     * @param $content
     * @param array $recipients
     * @param $sendername
     */
    public function __construct($content, array $recipients, $sendername)
    {
        $this->setContent($content);
        $this->setRecipients($recipients);
        $this->setSenderName($sendername);
    }

    /**
     * Get array with msisdns of recipients
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Get string containing the sendername
     * @return string
     */
    public function getSenderName()
    {
        return $this->sendername;
    }

    /**
     * Get string containing the content
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set array with msisdns of the recipients
     * @param array $recipients
     * @return $this
     */
    public function setRecipients(array $recipients)
    {
        foreach($recipients as $recipient)
            $this->addRecipient($recipient);

        return $this;
    }

    /**
     * Add a recipient to the array with msisdn
     * @param $recipient
     * @return $this
     */
    public function addRecipient($recipient)
    {
        $this->recipients[]	= $recipient;

        return $this;
    }

    /**
     * Set the sendername and throw an exception if invalid
     * @param $sendername
     * @return $this
     * @throws \Exception
     */
    public function setSenderName($sendername)
    {
        if(strlen($sendername) > 16 OR strlen($sendername) < 4)
            throw new \Exception('Invalid sendername');

        $this->sendername 	= $sendername;

        return $this;
    }

    /**
     * Set the content of the message
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content 		= $content;

        return $this;
    }

}