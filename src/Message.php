<?php 
namespace Vdbelt\InmobileSmsApi;

class Message
{

	protected $content 		= '';
	protected $sendername 	= '';
	protected $recipients 	= array();

	public function __construct($content, array $recipients, $sendername)
	{
		$this->setContent($content);
		$this->setRecipients($recipients);
		$this->setSenderName($sendername);

		return $this;
	}

	public function getRecipients()
	{
		return $this->recipients;
	}

	public function getSenderName()
	{
		return $this->sendername;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function setRecipients(array $recipients)
	{
		foreach($recipients as $recipient)
			$this->addRecipient($recipient);

		return $this;
	}

	public function addRecipient($recipient)
	{
		$this->recipients[]	= $recipient;

		return $this;
	}

	public function setSenderName($sendername)
	{
		if(strlen($sendername) > 16 OR strlen($sendername) < 4)
			throw new \Exception('Invalid sendername');

		$this->sendername 	= $sendername;

		return $this;
	}

	public function setContent($content)
	{
		$this->content 		= $content;

		return $this;
	}

}