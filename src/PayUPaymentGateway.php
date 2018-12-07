<?php

namespace Kinedu\PaymentGateways;

use Exception;
use InvalidArgumentException;
use Kinedu\PaymentGateways\PayU\{
    Card as PayUCard,
    Charge as PayUCharge,
    Customer as PayUCustomer
};
use PayU\{
    Environment as PayUEnvironment,
    PayU
};

class PayUPaymentGateway implements PaymentGateway
{
    public function __construct(array $settings)
    {
        foreach ([
            'api_key',
            'api_login',
            'merchant_id',
        ] as $key) {
            if (! array_key_exists($key, $settings)) {
                throw new InvalidArgumentException(
                    "The {$this->getProviderName()} SDK cannot be initialized without the '$key' setting."
                );
            }
        }

        PayU::$apiKey = $settings['api_key'];
        PayU::$apiLogin = $settings['api_login'];
        PayU::$merchantId = $settings['merchant_id'];
        PayU::$isTest = isset($settings['is_test']) ? $settings['is_test'] : true;

        if (array_key_exists('payments_url', $settings)) {
            PayUEnvironment::setPaymentsCustomUrl($settings['payments_url']);
        }

        if (array_key_exists('queries_url', $settings)) {
            PayUEnvironment::setReportsCustomUrl($settings['queries_url']);
        }

        if (array_key_exists('subscriptions_url', $settings)) {
            PayUEnvironment::setSubscriptionsCustomUrl($settings['subscriptions_url']);
        }
    }

    /**
     * Return a collection of customers from PayU.
     *
     * @param  array  $params
     * @throws \Exception
     */
    public function getAllCustomers(array $params = [])
    {
        throw new Exception("{$this->getProviderName()} does not support this feature.");
    }

    /**
     * Create a new customer.
     *
     * @param  array  $data  The customer information to be stored.
     * @return \Kinedu\PaymentGateways\PayU\Customer
     */
    public function createCustomer(array $data)
    {
        return PayUCustomer::create($data);
    }

    /**
     * Return the customer with the matching customer token ID.
     *
     * @param  string  $customerId
     * @return \Kinedu\PaymentGateways\PayU\Customer
     */
    public function getCustomer(string $customerId)
    {
        return PayUCustomer::find($customerId);
    }

    /**
     * Add a new customer card.
     *
     * @param  string  $customerId
     * @param  string  $token
     * @throws \Exception
     */
    public function addCard(string $customerId, string $token)
    {
        throw new Exception('This feature is not currently supported.');
    }

    /**
     * Find the card with the specified token.
     *
     * @param  string  $token
     * @return \Kinedu\PaymentGateways\PayU\Card
     */
    public function getCard(string $token)
    {
        return PayUCard::find($token);
    }

    /**
     * Return a listing of all cards belonging to the specified customer.
     *
     * @param  string  $customerId
     * @throws \Exception
     */
    public function getAllCards(string $customerId)
    {
        throw new Exception('This feature is not currently supported.');
    }

    /**
     * Return a collection of all charges made to the customer.
     *
     * @param  array  $params
     * @throws \Exception
     */
    public function getAllCharges(array $params = [])
    {
        throw new Exception('This feature is not currently supported.');
    }

    /**
     * Charge the customer the given amount.
     *
     * @param  float  $amount
     * @param  array  $options
     * @return \Kinedu\PaymentGateways\PayU\Charge
     */
    public function charge(float $amount, array $options = [])
    {
        return PayUCharge::create($amount, $options);
    }

    /**
     * Return the operation with the given transaction ID.
     *
     * @param  string  $transactionId
     * @throws \Exception
     */
    public function getOperation(string $transactionId)
    {
        throw new Exception('This feature is not currently supported.');
    }

    /**
     * Return the name of the current payment provider.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return 'PayU';
    }
}
