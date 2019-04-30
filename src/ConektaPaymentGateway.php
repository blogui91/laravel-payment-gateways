<?php

namespace Kinedu\PaymentGateways;

use Exception;
use Conekta\Conekta;
use InvalidArgumentException;
use Kinedu\PaymentGateways\Conekta\{
    Card as ConektaCard,
    Charge as ConektaCharge,
    Customer as ConektaCustomer,
    Operation as ConektaOperation
};

class ConektaPaymentGateway implements PaymentGateway
{
    public function __construct(string $apiKey)
    {
        Conekta::setApiKey($apiKey);
    }

    /**
     * Return a collection of customers from Conekta.
     *
     * @param  array  $params
     * @return \Kinedu\PaymentGateways\Conekta\Collection
     */
    public function getAllCustomers(array $params = [])
    {
        return ConektaCustomer::all($params);
    }

    /**
     * Create a new customer.
     *
     * @param  array  $data  The customer information to be stored.
     * @return \Kinedu\PaymentGateways\Conekta\Customer
     */
    public function createCustomer(array $data)
    {
        return ConektaCustomer::create($data);
    }

    /**
     * Add a new customer card.
     *
     * @param  string  $customerId
     * @param  string  $token
     * @return \Kinedu\PaymentGateways\Conekta\Card
     */
    public function addCard(string $customerId, string $token)
    {
        return ConektaCard::create($customerId, $token);
    }

    /**
     * @throws \Exception
     */
    public function getAllCards(string $customerId)
    {
        throw new Exception("{$this->getProviderName()} does not support this feature.");
    }

    /**
     * Return a collection of all charges made to the customer.
     *
     * @param  array  $params
     * @return \Kinedu\PaymentGateways\Conekta\Collection
     */
    public function getAllCharges(array $params = [])
    {
        return ConektaCharge::all($params);
    }

    /**
     * Charge the customer the given amount.
     *
     * @param  float  $amount
     * @param  array  $options
     * @return \Kinedu\PaymentGateways\Conekta\Charges
     *
     * @throws \InvalidArgumentException
     */
    public function charge(float $amount, array $options = [])
    {
        if (! array_key_exists('customer_id', $options)) {
            throw new InvalidArgumentException(
                'The customer_id field is required within the $options array'
            );
        }

        if (! array_key_exists('card_token', $options)
            && ! array_key_exists('payment_source_id', $options))
        {
            throw new InvalidArgumentException(
                'The card_token field or payment_source_id field is required within the $options array'
            );
        }

        return ConektaCharge::create($amount, $options);
    }

    /**
     * Return the operation with the given transaction ID.
     *
     * @param  string  $transactionId
     * @return \Kinedu\PaymentGateways\Conekta\Operation
     */
    public function getOperation(string $transactionId)
    {
        return ConektaOperation::find($transactionId);
    }

    /**
     * Return the name of the current payment provider.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return 'Conekta';
    }
}
