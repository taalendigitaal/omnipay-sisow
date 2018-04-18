<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Sisow\Message\AbstractResponse;

class CreditInvoiceResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        return isset($this->data->creditinvoice);
    }

    public function getInvoiceNumber()
    {
        return $this->data->creditinvoice->invoiceno;
    }

    public function getDocumentId()
    {
        return $this->data->creditinvoice->documentid;
    }

}
