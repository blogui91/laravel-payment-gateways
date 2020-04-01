<?php

namespace Kinedu\PaymentGateways\Conekta;

use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use Conekta\{
    Charge as ConektaCharge,
    Order as ConektaOrder,
    Handler as ConektaError
};

class Charge extends ApiResource
{
    const OBJECT_NAME = 'charge';

    /**
     * Retrieve a listing of all charges made.
     *
     * @param  array  $parameters
     * @return \Kinedu\PaymentGateways\Conekta\Charge
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function all(array $parameters = [])
    {
        try {
            $charges = ConektaCharge::all($parameters);
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        $response = array_merge($charges, [
            'object' => 'list',
            'data' => Util::formatData($charges['charges'], static::OBJECT_NAME),
        ]);
        unset($response['charges']);

        return static::convertToObject($response);
    }

    /**
     * Store a new credit card for the specified customer.
     *
     * @param  int  $amount
     * @param  array  $options
     * @return \Kinedu\PaymentGateways\Conekta\Charge
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function create(int $amount, array $options)
    {
        $payload = [
            'currency' => 'MXN',
            'customer_info' => [
                'customer_id' => $options['customer_id'],
            ],
            'line_items' => [
                [
                    'name' => 'Servicios de Guardería',
                    'unit_price' => $amount,
                    'quantity' => 1,
                ],
            ],
            'charges' => [
                [
                    'payment_method' => [
                        'type' => 'card',
                    ],
                    'amount' => $amount,
                ],
            ],
        ];

        if (array_key_exists('card_token', $options)) {
            $payload['charges'][0]['payment_method']['token_id'] = $options['card_token'];
        } else {
            $payload['charges'][0]['payment_method']['payment_source_id'] = $options['payment_source_id'];
        }

        try {
            $order = ConektaOrder::create($payload);
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject($order);
    }

    /**
     * Retrieve the specified credit card belonging to the specified customer.
     *
     * @param  string  $transactionId
     * @return \Kinedu\PaymentGateways\Conekta\Charge
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function find(string $transactionId)
    {
        try {
            $charge = ConektaCharge::find($transactionId);
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject($charge);
    }

    /**
     * Convert the charge array to an object.
     *
     * @param  array|object  $charge
     * @return array
     */
    private static function convertToObject($charge)
    {
        return Util::convertToObject(static::normalizeCharge($charge));
    }

    /**
     * Normalize the charge data.
     *
     * @param  array|object  $charge
     * @return array
     */
    private static function normalizeCharge($charge): array
    {
        $charge = (array) $charge;

        return [
            'order_id' => $charge['id'],
            'transaction' => $charge['id'],
            'status' => $charge['payment_status'],
            'timestamp' => $charge['created_at'],
        ];
    }
}
