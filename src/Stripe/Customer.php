<?php

namespace Kinedu\PaymentGateways\Stripe;

use Exception;
use Kinedu\PaymentGateways\Util\Util;
use Stripe\Customer as StripeCustomer;
use Kinedu\PaymentGateways\ApiResource;

class Customer extends ApiResource
{
    /** @var string */
    const OBJECT_NAME = 'customer';

    /**
     * Create a new customer.
     *
     * @param  array  $data  The customer information to be stored.
     *
     * @throws \Exception
     *
     * @return \Kinedu\PaymentGateways\Stripe\Customer
     */
    public static function create(array $data)
    {
        try {
            $customer = StripeCustomer::create($data);
        } catch (Exception $e) {
            throw $e;
        }

        return Util::convertToObject($customer);
    }
}

