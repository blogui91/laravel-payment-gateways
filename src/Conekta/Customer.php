<?php

namespace Kinedu\PaymentGateways\Conekta;

use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use Conekta\{
    Customer as ConektaCustomer,
    Handler as ConektaError
};

class Customer extends ApiResource
{
    const OBJECT_NAME = 'customer';

    /**
     * Retrieve a listing of all Conekta customers.
     *
     * @param  array  $parameters
     * @return \Kinedu\PaymentGateways\Conekta\Collection
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function all($parameters = [])
    {
        try {
            $customers = ConektaCustomer::all($parameters);
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        $response = array_merge($customers, [
            'object' => 'list',
            'data' => Util::formatData($customers['customers'], static::OBJECT_NAME),
        ]);
        unset($response['customers']);

        return static::convertToObject((array) $response);
    }

    /**
     * Create a new customer.
     *
     * @param  array  $data  The customer information to be stored.
     * @return \Kinedu\PaymentGateways\Conekta\Customer
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function create(array $data)
    {
        try {
            $customer = ConektaCustomer::create($data);
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject((array) $customer);
    }

    /**
     * Retrieve the customer with the matching customer token ID.
     *
     * @param  string  $id
     * @return \Kinedu\PaymentGateways\Conekta\Customer
     */
    public static function find(string $id)
    {
        $customer = ConektaCustomer::find($id);

        return static::convertToObject((array) $customer);
    }

    /**
     * Convert the customer array to an object.
     *
     * @param  array  $customer
     * @return array
     */
    private static function convertToObject(array $customer)
    {
        return Util::convertToSrPagoObject($customer);
    }
}
