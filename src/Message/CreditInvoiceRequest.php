<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Sisow\Message\InvoiceRequest;
use Omnipay\Sisow\Message\CreditInvoiceResponse;
use Guzzle\Http\Message\Response;

class CreditInvoiceRequest extends InvoiceRequest
{

    protected $endpoint = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/CreditInvoiceRequest';

    public function buildResponse(Response $response)
    {
        return $this->response = new CreditInvoiceResponse($this, $this->parseXmlResponse($response));
    }

}
