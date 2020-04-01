<?php

namespace Kinedu\PaymentGateways\Stripe;

use Exception;
use InvalidArgumentException;
use Stripe\Charge as StripeCharge;
use Kinedu\PaymentGateways\Util\Util;
use Kinedu\PaymentGateways\ApiResource;

class Charge extends ApiResource
{
    /** @var string */
    const OBJECT_NAME = 'charge';

    /**
     * Store a new charge for the specified customer.
     *
     * @param  float  $amount
     * @param  array  $options
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     *
     * @return \Kinedu\PaymentGateways\Stripe\Charge
     */
    public static function create(float $amount, array $options = [])
    {
        if (! isset($options['source']) && ! isset($options['customer'])) {
            throw new InvalidArgumentException('A card token and customer id is required to know which card to charge.');
        }

        if (! isset($options['currency'])) {
            throw new InvalidArgumentException('The currency is required.');
        }

        try {
            $charge = StripeCharge::create(array_merge([
                'amount' => $amount,
            ], $options));
        } catch (Exception $e) {
            throw $e;
        }

        return Util::convertToObject($charge);
    }

    /**
     * Retrieve the specified charge.
     *
     * @param  string  $transactionId
     *
     * @throws \Exception
     *
     * @return \Kinedu\PaymentGateways\Stripe\Charge
     */
    public static function find(string $transactionId)
    {
        try {
            $charge = StripeCharge::retrieve($transactionId);
        } catch (Exception $e) {
            throw $e;
        }

        return Util::convertToObject($charge);
    }
}

