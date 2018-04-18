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
        return $this->buildResponse($this->httpClient->request('POST', $this->endpoint, [], http_build_query($data)));
    }

    public function buildResponse(Response $response)
    {
        return $this->response = new InvoiceResponse($this, $this->parseXmlResponse($response));
    }

}
