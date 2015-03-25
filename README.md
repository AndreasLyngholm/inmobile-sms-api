# Inmobile SMS API Client for PHP

The Inmobile SMS API Client Library can be used for interaction with the Inmobile API.

## Installation

Require this package with composer:

```
composer require vdbelt/inmobile-sms-api
```

## Usage

```php
<?php

$Message = new VdBelt\InmobileSmsApi\Message(
    'Hello world!',
    array('4500000000'), // an array containing the destination(s)
    '4512345678' // from, max. 16 chars or valid MSISDN
);

$Inmobile = new Vdbelt\InmobileSmsApi\Connector('<YOUR API KEY>');
$Inmobile->addMessage($Message);
$Inmobile->send();
```

## Reporting Issues
Report issues using the [Github Issue Tracker](https://github.com/vdbelt/inmobile-sms-api/issues) or email [martin@vandebelt.dk](mailto:martin@vandebelt.dk).

## Copyright
Copyright Martin van de Belt. See LICENSE for details.
