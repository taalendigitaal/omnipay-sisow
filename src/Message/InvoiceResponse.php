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
        if (isset($this->data->invoice) && isset($this->data->invoice->invoiceno)) {
            return (string) $this->data->invoice->invoiceno;
        }

        return null;
    }

    public function getDocumentId()
    {
        if (isset($this->data->invoice) && isset($this->data->invoice->documentid)) {
            return (string) $this->data->invoice->documentid;
        }

        return null;
    }

    public function getDocumentUrl()
    {
        if (isset($this->data->invoice) && isset($this->data->invoice->documenturl)) {
            return (string) $this->data->invoice->documenturl;
        }

        return null;
    }

}
