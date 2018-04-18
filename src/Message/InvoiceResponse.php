<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Sisow\Message\AbstractResponse;

class InvoiceResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        return isset($this->data->invoice);
    }

    public function getInvoiceNumber()
    {
        return $this->data->invoice->invoiceno;
    }

    public function getDocumentId()
    {
        return $this->data->invoice->documentid;
    }

}
