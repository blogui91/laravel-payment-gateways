<?php

namespace Kinedu\PaymentGateways\SrPago;

use DateTime;
use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use SrPago\{
    Charges as SrPagoCharges,
    Error\SrPagoError
};

class Charge extends ApiResource
{
    const OBJECT_NAME = 'charge';

    /**
     * Retrieve a listing of all charges made.
     *
     * @param  array  $parameters
     * @return \Kinedu\PaymentGateways\SrPago\Charge
     *
     * @throws \SrPago\Error\SrPagoError|\Exception
     */
    public static function all(array $parameters = [])
    {
        try {
            $charges = (new SrPagoCharges())->all($parameters);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        $response = array_merge($charges, [
            'object' => 'list',
            'data' => Util::formatData($charges['operations'], static::OBJECT_NAME),
        ]);
        unset($response['operations']);

        return static::convertToObject($response);
    }

    /**
     * Store a new credit card for the specified customer.
     *
     * @param  float  $amount
     * @param  array  $options
     * @return \Kinedu\PaymentGateways\SrPago\Charge
     *
     * @throws \InvalidArgumentException|\SrPago\Error\SrPagoError|\Exception
     */
    public static function create(float $amount, array $options = [])
    {
        if (! isset($options['card_token'])) {
            throw new InvalidArgumentException('A card token is required to know which card to charge.');
        }

        $data = array_merge([
            'amount' => number_format($amount, 2, '.', ''),
            'source' => $options['card_token'],
        ], $options);

        try {
            $charge = (new SrPagoCharges())->create($data);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject($charge);
    }

    /**
     * Retrieve the specified credit card belonging to the specified customer.
     *
     * @param  string  $transactionId
     * @return \Kinedu\PaymentGateways\SrPago\Charge
     *
     * @throws \SrPago\Error\SrPagoError|\Exception
     */
    public static function find(string $transactionId)
    {
        try {
            $charge = (new SrPagoCharges())->retreive($transactionId);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject($charge);
    }

    /**
     * Convert the charge array to an object.
     *
     * @param  array  $charge
     * @return array
     */
    private static function convertToObject(array $charge)
    {
        return Util::convertToSrPagoObject($charge);
    }
}
