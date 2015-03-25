<?php 
namespace Vdbelt\InmobileSmsApi;

use GuzzleHttp\Client;

class Connector
{

	protected $endpoint 	= 'https://mm.inmobile.dk';
	protected $api_key		= '';
	protected $messages 	= array();

	public function __construct($api_key, $endpoint = null)
	{
		$this->api_key 		= $api_key;

		if(!is_null($endpoint))
			$this->setEndpoint($endpoint);
	}

	public function getEndpoint($endpoint)
	{
		return $this->endpoint;
	}

	public function getApiKey()
	{
		return $this->api_key;
	}

	public function getMessages()
	{
		return $this->messages;
	}

	public function setEndpoint($endpoint)
	{
		$this->endpoint 	= $endpoint;

		return $this;
	}

	public function setApiKey($api_key)
	{
		$this->api_key		= $api_key;

		return $this;
	}

	public function addMessage(Message $Message)
	{
		$this->messages[]	= $Message;

		return $this;
	}

	public function getHttpClient()
	{
		return new Client;
	}

	public function send()
	{
		if(count($this->messages) < 1)
			throw new \Exception('No messages to send');

		$builder 			= new XML_Payload_Builder($this);
		$client 			= $this->getHttpClient();

		$client->post($this->endpoint.'/Api/V2/SendMessages', [
			'body' => [
				'xml' => $builder->getXML()
		]]);

		return true;
	}

}


class XML_Payload_Builder
{

	private $dom 			= null;
	private $connector 		= null;

	public function __construct(Connector $Connector)
	{
		$this->connector 	= $Connector;
		$this->dom 			= new \DomDocument('1.0', 'UTF-8');

		$request 			= $this->dom->createElement('request');

		$request->appendChild($this->buildAuthenticationHeader());
		$request->appendChild($this->buildMessages());

		$this->dom->appendChild($request);
	}

	public function getXML()
	{
		return $this->dom->saveXML();
	}

	private function buildAuthenticationHeader()
	{
		$authentication 	= $this->dom->createElement('authentication');
		$authentication->setAttribute('apikey', $this->connector->getApiKey());
		
		return $authentication;
	}

	private function buildMessages()
	{
		$data = $this->dom->createElement('data');

		foreach($this->connector->getMessages() as $Message)
		{
			$element 		= $this->dom->createElement('message');
			$sendername 	= $this->dom->createElement('sendername', $Message->getSenderName());
			$text 			= $this->dom->createElement('text');
			$recipients 	= $this->dom->createElement('recipients');

			foreach($Message->getRecipients() as $Recipient)
			{
				$msisdn 	= $this->dom->createElement('msisdn', $Recipient);
				$recipients->appendChild($msisdn);
			}

			$text->appendChild($this->dom->createCDATASection($Message->getContent()));

			$element->appendChild($sendername);
			$element->appendChild($text);
			$element->appendChild($recipients);

			$data->appendChild($element);
		}

		return $data;
	}

}