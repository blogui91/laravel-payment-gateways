<?php

namespace Kinedu\PaymentGateways\SrPago;

use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use SrPago\{
    Customer as SrPagoCustomer,
    Error\SrPagoError
};

class Customer extends ApiResource
{
    const OBJECT_NAME = 'customer';

    /**
     * Retrieve a listing of all SrPago customers.
     *
     * @param  array  $parameters
     * @return \Kinedu\PaymentGateways\SrPago\Collection
     */
    public static function all($parameters = [])
    {
        try {
            $customers = (new SrPagoCustomer())->all($parameters);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        $response = array_merge($customers, [
            'object' => 'list',
            'data' => Util::formatData($customers['customers'], static::OBJECT_NAME),
        ]);
        unset($response['customers']);

        return static::convertToObject($response);
    }

    /**
     * Create a new customer.
     *
     * @param  array  $data  The customer information to be stored.
     * @return \Kinedu\PaymentGateways\SrPago\Customer
     *
     * @throws \SrPago\Error\SrPagoError|Exception
     */
    public static function create(array $data)
    {
        try {
            $customer = (new SrPagoCustomer())->create($data);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject($customer);
    }

    /**
     * Retrieve the customer with the matching customer token ID.
     *
     * @param  string  $id
     * @return \Kinedu\PaymentGateways\SrPago\Customer
     */
    public static function find(string $id)
    {
        $customer = (new SrPagoCustomer())->find($id);

        return static::convertToObject($customer);
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
