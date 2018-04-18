<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Sisow\Message\InvoiceRequest;
use Omnipay\Sisow\Message\CancelReservationResponse;
use Guzzle\Http\Message\Response;

class CancelReservationRequest extends InvoiceRequest
{

    protected $endpoint = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/CancelReservationRequest';

    public function buildResponse(Response $response)
    {
        return $this->response = new CancelReservationResponse($this, $this->parseXmlResponse($response));
    }

}
