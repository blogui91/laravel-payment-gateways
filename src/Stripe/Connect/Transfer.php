<?php

namespace Kinedu\PaymentGateways\Stripe\Connect;

use Exception;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Kinedu\PaymentGateways\Util\Util;
use Stripe\Transfer as StripeTransfer;
use Kinedu\PaymentGateways\ApiResource;

class Transfer extends ApiResource
{
    /** @var string */
    const OBJECT_NAME = 'transfer';

    /**
     * Create a new transfer.
     *
     * @param  array  $data
     *
     * @throws \Exception
     *
     * @return \Kinedu\PaymentGateways\Stripe\Connect\Transfer
     */
    public static function create(array $data)
    {
        if (! Arr::get($data, 'amount')) {
            throw new InvalidArgumentException('Amount is required.');
        }

        if (! Arr::get($data, 'currency')) {
            throw new InvalidArgumentException('Currency is required');
        }

        if (! Arr::get($data, 'destination')) {
            throw new InvalidArgumentException('Destination is required');
        }

        try {
            $transfer = StripeTransfer::createPerson($data);
        } catch (Exception $e) {
            throw $e;
        }

        return Util::convertToObject($person);
    }
}
