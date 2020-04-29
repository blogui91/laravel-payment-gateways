<?php

namespace Kinedu\PaymentGateways\Stripe;

use Exception;
use Stripe\Token as StripeToken;
use Kinedu\PaymentGateways\Util\Util;
use Kinedu\PaymentGateways\ApiResource;

class Token extends ApiResource
{
    /** @var string */
    const OBJECT_NAME = 'token';

    /**
     * Get the given token.
     *
     * @param  string  $token
     *
     * @return \Kinedu\PaymentGateways\Stripe\Token
     */
    public function get(string $token)
    {
        try {
            $token = StripeToken::retrieve($token);
        } catch (Exception $e) {
            throw $e;
        }

        return Util::convertToObject($token);
    }
}
