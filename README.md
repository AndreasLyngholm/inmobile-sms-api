# Inmobile SMS API Client for PHP

The Inmobile SMS API Client Library can be used for interaction with the Inmobile API.

## Installation

Require this package with composer:

```
composer require andreaslyngholm/inmobile-sms-api
```

## Basic usage

```php
<?php

use Inmobile\Text;
use Inmobile\Gateway;
use Inmobile\Message;
use Inmobile\Recipient;

$gateway = Gateway::create('apiKey');

$message = Message::create('Hello world!')->from('My App')->to('4512345678');

$gateway->addMessage($message);
$gateway->send();


// Other capabilities:
$text = (new Text('Hello World'))->flash()->encoding('utf-8');

$recipients = [
    (new Recipient('4512345678'))->withMessageId('my-id'),
    '450000000'
];

Message::create($text)->to($recipients)->from('My App')->doNotRespectBlacklist()->scheduleAt(date_create('+1 hour'));