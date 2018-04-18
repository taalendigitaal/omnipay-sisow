<?php

namespace Omnipay\Sisow\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Sisow Purchase Request
 */
class PurchaseRequest extends AbstractRequest
{

    protected $endpoint = 'https://www.sisow.nl/Sisow/iDeal/RestHandler.ashx/TransactionRequest';

    public function getDays()
    {
        return $this->getParameter('days');
    }

    public function setDays($value)
    {
        return $this->setParameter('days', $value);
    }

    public function getIncluding()
    {
        return $this->getParameter('including');
    }

    public function setIncluding($value)
    {
        return $this->setParameter('including', $value);
    }

    public function getEntranceCode()
    {
        return $this->getParameter('entranceCode') ?: $this->getTransactionId();
    }

    public function setEntranceCode($value)
    {
        return $this->setParameter('entranceCode', $value);
    }

    public function getBic()
    {
        return $this->getParameter('bic');
    }

    public function setBic($value)
    {
        return $this->setParameter('bic', $value);
    }

    public function getIban()
    {
        return $this->getParameter('iban');
    }

    public function setIban($value)
    {
        return $this->setParameter('iban', $value);
    }

    public function getCoc()
    {
        return $this->getParameter('coc');
    }

    public function setCoc($value)
    {
        return $this->setParameter('coc', $value);
    }

    public function getBillingCountrycode()
    {
        return $this->getParameter('billingCountrycode');
    }

    public function setBillingCountrycode($value)
    {
        return $this->setParameter('billingCountrycode', $value);
    }

    public function getShippingCountrycode()
    {
        return $this->getParameter('shippingCountrycode');
    }

    public function setShippingCountrycode($value)
    {
        return $this->setParameter('shippingCountrycode', $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function generateSignature()
    {
        return sha1(
            $this->getTransactionId() . $this->getEntranceCode() . $this->getAmountInteger() .
            $this->getShopId() . $this->getMerchantId() . $this->getMerchantKey()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->validate(
            'amount',
            'transactionId',
            'returnUrl',
            'notifyUrl'
        );

        if (!$this->getTestMode() && $this->getIssuer() == 99) {
            throw new InvalidRequestException("The issuer can only be '99' in testMode!");
        }

        $data = [
            'shopid' => $this->getShopId(),
            'merchantid' => $this->getMerchantId(),
            'merchantkey' => $this->getMerchantKey(),
            'payment' => $this->getPaymentMethod(),
            'purchaseid' => $this->getTransactionId(),
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmountInteger(),
            'entrancecode' => $this->getEntranceCode(),
            'description' => $this->getDescription(),
            'returnurl' => $this->getReturnUrl(),
            'cancelurl' => $this->getCancelUrl(),
            'notifyurl' => $this->getNotifyUrl(),
            'sha1' => $this->generateSignature(),
            'testmode' => $this->getTestMode() ? 'true' : null,
        ];

        if (in_array($this->getPaymentMethod(), ['ideal', '', null])) {
            $data['issuerid'] = $this->getIssuer();
        }

        if (in_array($this->getPaymentMethod(), ['overboeking', 'ebill'])) {
            $data['including'] = $this->getIncluding();
            $data['days'] = $this->getDays();
        }

        if (in_array($this->getPaymentMethod(), ['giropay', 'eps'])) {
            $data['bic'] = $this->getBic();
        }

        if (in_array($this->getPaymentMethod(), ['focum'])) {
            $data['iban'] = $this->getIban();
        }

        if (in_array($this->getPaymentMethod(), ['focum', 'klarna', 'afterpay', 'capayable'])) {
            $data['ipaddress'] = $this->getClientIp();
        }

        /** @var \Omnipay\Common\CreditCard $card */
        $card = $this->getCard();
        if ($card) {
            if (in_array($this->getPaymentMethod(), ['overboeking', 'ebill', 'focum', 'klarna', 'afterpay', 'capayable'])) {
                $data['billing_mail'] = $card->getEmail();
                $data['billing_firstname'] = $card->getBillingFirstName();
                $data['billing_lastname'] = $card->getBillingLastName();
                $data['billing_countrycode'] = $this->getBillingCountrycode();
            }

            if (in_array($this->getPaymentMethod(), ['focum', 'klarna', 'afterpay', 'capayable'])) {
                $data['billing_company'] = $card->getBillingCompany();
                if ($this->getCoc()) {
                    $data['billing_coc'] = $this->getCoc();
                }
                $data['billing_address1'] = $card->getBillingAddress1();
                $data['billing_address2'] = $card->getBillingAddress2();
                $data['billing_zip'] = $card->getBillingPostcode();
                $data['billing_city'] = $card->getBillingCity();
                $data['billing_country'] = $card->getBillingCountry();
                $data['billing_phone'] = $card->getBillingPhone();

                $data['birthdate'] = $card->getBirthday() ? date('dmY', strtotime($card->getBirthday())) : null;
                $data['gender'] = in_array(substr($card->getGender(), 0, 1), ['f', 'm']) ? substr($card->getGender(), 0, 1) : null;

                $data['shipping_mail'] = $card->getEmail();
                $data['shipping_firstname'] = $card->getShippingFirstName();
                $data['shipping_lastname'] = $card->getShippingLastName();
                $data['shipping_company'] = $card->getShippingCompany();
                $data['shipping_address1'] = $card->getShippingAddress1();
                $data['shipping_address2'] = $card->getShippingAddress2();
                $data['shipping_zip'] = $card->getShippingPostcode();
                $data['shipping_city'] = $card->getShippingCity();
                $data['shipping_country'] = $card->getShippingCountry();
                $data['shipping_countrycode'] = $this->getShippingCountrycode();
                $data['shipping_phone'] = $card->getShippingPhone();

                $data = array_merge($data, $this->getItemData());
            }
        }

        return $data;
    }

    protected function getItemData()
    {
        $data = array();
        $items = $this->getItems();

        if ($items) {
            foreach ($items as $i => $item) {
                $x = $i + 1;
                $data['product_id_' . $x] = $item->getName();
                $data['product_description_' . $x] = $item->getDescription();
                $data['product_quantity_' . $x] = $item->getQuantity();
                $data['product_netprice_' . $x] = round(($this->formatCurrency($item->getPrice()) / 121 * 100) * 100);
                $data['product_total_' . $x] = round(
                    $this->formatCurrency($item->getPrice()) * $item->getQuantity() * 100
                );
                $data['product_nettotal_' . $x] = round(
                    ($this->formatCurrency($item->getPrice()) / 121 * 100) * $item->getQuantity() * 100
                );

                //@todo fix tax rates
                $data['product_tax_' . $x] = round(($this->formatCurrency($item->getPrice()) / 121 * 21) * 100);
                $data['product_taxrate_' . $x] = 21 * 100;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function sendData($data)
    {
        $httpResponse = $this->httpClient->post($this->endpoint, null, $data)->send();

        return $this->response = new PurchaseResponse($this, $httpResponse->xml());
    }

}
