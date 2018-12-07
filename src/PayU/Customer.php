<?php

namespace Kinedu\PaymentGateways\PayU;

use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use PayU\{
    PayUCustomers,
    PayUParameters
};

class Customer extends ApiResource
{
    const OBJECT_NAME = 'payu_customer';

    /**
     * Create a new customer in the PayU API.
     *
     * @param  array  $data  The customer information to be stored.
     * @return \Kinedu\PaymentGateways\PayU\Customer
     *
     * @throws \PayU\PayUException|Exception
     */
    public static function create(array $data)
    {
        try {
            $customer = PayUCustomers::create($data);
        } catch (Exception $e) {
            \Log::error($e);
            throw $e;
        }

        return static::convertToObject($customer);
    }

    /**
     * Retrieve the customer with the matching customer token ID.
     *
     * @param  string  $customerId
     * @return \Kinedu\PaymentGateways\PayU\Customer
     */
    public static function find(string $customerId)
    {
        $customer = PayUCustomers::find([
            PayUParameters::CUSTOMER_ID => $customerId,
        ]);

        return static::convertToObject($customer);
    }

    /**
     * Convert the customer array to an object.
     *
     * @param  array|object  $customer
     * @return \Kinedu\PaymentGateways\PayU\Customer
     */
    private static function convertToObject($customer)
    {
        return Util::convertToSrPagoObject(
            Util::formatData((array) $customer, static::OBJECT_NAME)
        );
    }
}
