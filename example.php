<?php 

$Inm_Connector = new Vdbelt\InmobileSmsApi\Connector('<YOUR API KEY>');

$Inm_Connector->addMessage(new VdBelt\InmobileSmsApi\Message('<CONTENT>', array('4500000000'), '<SENDERNAME>'))->send();