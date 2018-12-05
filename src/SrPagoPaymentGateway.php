<?php

namespace Kinedu\PaymentGateways;

use SrPago\SrPago;
use InvalidArgumentException;
use Kinedu\PaymentGateways\SrPago\{
    Card as SrPagoCard,
    Charge as SrPagoCharge,
    Customer as SrPagoCustomer,
    Operation as SrPagoOperation
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
     * Return a collection of customers from SrPago.
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
     * Return a listing of all cards belonging to the specified customer.
     *
     * @param  string  $customerId
     * @return \Kinedu\PaymentGateways\SrPago\Collection
     */
    public function getAllCards(string $customerId)
    {
        return SrPagoCard::all($customerId);
    }

    /**
     * Return a collection of all charges made to the customer.
     *
     * @param  array  $params
     * @return \Kinedu\PaymentGateways\SrPago\Collection
     */
    public function getAllCharges(array $params = [])
    {
        return SrPagoCharge::all($params);
    }

    /**
     * Charge the customer the given amount.
     *
     * @param  float  $amount
     * @param  array  $options
     * @return \Kinedu\PaymentGateways\SrPago\Charges
     */
    public function charge(float $amount, array $options = [])
    {
        return SrPagoCharge::create($amount, $options);
    }

    /**
     * Return the operation with the given transaction ID.
     *
     * @param  string  $transactionId
     * @return \Kinedu\PaymentGateways\SrPago\Operation
     */
    public function getOperation(string $transactionId)
    {
        return SrPagoOperation::find($transactionId);
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
