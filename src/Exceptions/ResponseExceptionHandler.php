<?php


namespace Inmobile\Exceptions;


use Psr\Http\Message\ResponseInterface;

class ResponseExceptionHandler
{
    public function transformResponseToException(ResponseInterface $response) : ResponseInterface
    {
        $responseBody = $response->getBody();

        $responseBody->rewind();
        $possibleError = $responseBody->getContents();
        $responseBody->rewind();
        
        if(is_numeric($possibleError)) {
            throw new GatewayError($possibleError);
        }
        
        return $response;   
    }
}