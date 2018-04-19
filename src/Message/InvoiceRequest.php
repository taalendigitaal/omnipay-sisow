<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Sisow\Message\AbstractRequest;
use Omnipay\Sisow\Message\InvoiceResponse;
use Guzzle\Http\Message\Response;

class InvoiceRequest extends AbstractRequest
{

    protected $endpoint = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/InvoiceRequest';

    public function getTransactionReference()
    {
        return $this->getParameter('trxid');
    }

    public function setTransactionReference($value)
    {
        return $this->setParameter('trxid', $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function generateSignature()
    {
        return sha1(
            $this->getTransactionReference() .
            $this->getMerchantId() . $this->getMerchantKey()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->validate('merchantId', 'merchantKey', 'trxid');

        $data = array(
            'merchantid'    => $this->getMerchantId(),
            'merchantkey'   => $this->getMerchantKey(),
            'trxid'         => $this->getTransactionReference(),
            'sha1'          => $this->generateSignature(),
        );

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->endpoint, null, $data)->send();

        return $this->buildResponse($httpResponse);
    }

    public function buildResponse(Response $httpResponse)
    {
        return $this->response = new InvoiceResponse($this, $httpResponse->xml());
    }

}
