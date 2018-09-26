<?php

namespace Kinedu\PaymentGateways;

use SrPago\SrPago;
use Kinedu\PaymentGateways\SrPago\{
    Card as SrPagoCard,
    Customer as SrPagoCustomer
};

class SrPagoPaymentGateway implements PaymentGateway
{
    public function __construct(string $apiKey, string $apiSecret, bool $livemode = false)
    {
        SrPago::setLiveMode($livemode);
        SrPago::setApiKey($apiKey);
        SrPago::setApiSecret($apiSecret);
    }

    /**
     * Create a new customer.
     *
     * @param  array  $params
     * @return \Kinedu\PaymentGateways\SrPago\Collection
     */
    public function getAllCustomers(array $params = [])
    {
        return SrPagoCustomer::all($params);
    }

    /**
     * Create a new customer.
     *
     * @param  array  $data  The customer information to be stored.
     * @return \Kinedu\PaymentGateways\SrPago\Customer
     */
    public function createCustomer(array $data)
    {
        return SrPagoCustomer::create($data);
    }

    /**
     * Add a new customer card.
     *
     * @param  string  $customerId
     * @param  string  $token
     * @return \Kinedu\PaymentGateways\SrPago\Card
     */
    public function addCard(string $customerId, string $token)
    {
        return SrPagoCard::create($customerId, $token);
    }

    /**
     * Return the name of the current payment provider.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return 'SrPago';
    }
}
