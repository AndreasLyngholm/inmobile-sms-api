<?php namespace Vdbelt\InmobileSmsApi;

use GuzzleHttp\Client;

/**
 * Class Connector is responsible for connecting to the remote API
 * @package Vdbelt\InmobileSmsApi
 */
class Connector
{

    /**
     * The endpoint we wish to connect to
     * @var string
     */
    protected $endpoint     = 'https://mm.inmobile.dk';

    /**
     * The API key we are using
     * @var string
     */
    protected $api_key      = '';

    /**
     * The messages we are going to send
     * @var array
     */
    protected $messages     = array();

    /**
     * @param $api_key
     * @param null $endpoint
     */
    public function __construct($api_key, $endpoint = null)
	{
		$this->api_key 		= $api_key;

		if(!is_null($endpoint))
			$this->setEndpoint($endpoint);
	}

    /**
     * Get the current endpoint
     * @param $endpoint
     * @return string
     */
    public function getEndpoint()
	{
		return $this->endpoint;
	}

    /**
     * Get the current API key
     * @return string
     */
    public function getApiKey()
	{
		return $this->api_key;
	}

    /**
     * Get an array with the current message objects
     * @return array
     */
    public function getMessages()
	{
		return $this->messages;
	}

    /**
     * Set the endpoint
     * @param $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
	{
		$this->endpoint 	= $endpoint;

		return $this;
	}

    /**
     * Set the API key
     * @param $api_key
     * @return $this
     */
    public function setApiKey($api_key)
	{
		$this->api_key		= $api_key;

		return $this;
	}

    /**
     * Add a message to the payload
     * @param Message $Message
     * @return $this
     */
    public function addMessage(Message $Message)
	{
		$this->messages[]	= $Message;

		return $this;
	}

    /**
     * Send the actual payload to the endpoint
     * @return bool
     * @throws \Exception
     */
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

    /**
     * Get the http client using
     * @return Client
     */
    protected function getHttpClient()
    {
        return new Client;
    }

}


/**
 * Class XML_Payload_Builder builds the actual XML payload as expected by endpoint
 * @package Vdbelt\InmobileSmsApi
 */
class XML_Payload_Builder
{

    /**
     * The DomDocument object to use
     * @var \DomDocument|null
     */
    private $dom 			= null;

    /**
     * The Connector
     * @var null|Connector
     */
    private $connector 		= null;

    /**
     * @param Connector $Connector
     */
    public function __construct(Connector $Connector)
	{
		$this->connector 	= $Connector;
		$this->dom 			= new \DomDocument('1.0', 'UTF-8');

		$request 			= $this->dom->createElement('request');

		$request->appendChild($this->buildAuthenticationHeader());
		$request->appendChild($this->buildMessages());

		$this->dom->appendChild($request);
	}

    /**
     * Returns the XML payload
     * @return mixed
     */
    public function getXML()
	{
		return $this->dom->saveXML();
	}

    /**
     * Builds the authentication header to add
     * @return mixed
     */
    protected function buildAuthenticationHeader()
	{
		$authentication 	= $this->dom->createElement('authentication');
		$authentication->setAttribute('apikey', $this->connector->getApiKey());
		
		return $authentication;
	}

    /**
     * Transforms Message objects into XML
     * @return mixed
     */
    protected function buildMessages()
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