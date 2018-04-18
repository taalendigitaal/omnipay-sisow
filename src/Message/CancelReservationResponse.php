<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Sisow\Message\AbstractResponse;

class CancelReservationResponse extends AbstractResponse
{

    public function isSuccessful()
    {
        return isset($this->data->reservation);
    }

    public function getStatus()
    {
        return $this->data->reservation->status;
    }

}
