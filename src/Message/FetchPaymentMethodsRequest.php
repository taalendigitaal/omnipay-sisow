<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Common\Http\ResponseParser;

class FetchPaymentMethodsRequest extends AbstractRequest
{
    protected $endpoint = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/CheckMerchantRequest';

    /**
     *  Generate a signature
     */
    protected function generateSignature()
    {
        return sha1(
            $this->getMerchantId() .
            $this->getMerchantKey()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->validate('merchantId', 'merchantKey');

        $data = array();
        $data['merchantid'] = $this->getMerchantId();
        $data['merchantkey'] = $this->getMerchantKey();
        $data['sha1'] = $this->generateSignature();

        return $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->request('POST', $this->endpoint, [], http_build_query($data));

        return $this->response = new FetchPaymentMethodsResponse($this, $this->parseXmlResponse($httpResponse));
    }
}
