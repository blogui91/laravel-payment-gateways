<?php

namespace Kinedu\PaymentGateways\Stripe;

use Exception;
use Kinedu\PaymentGateways\Util\Util;
use Stripe\Customer as StripeCustomer;
use Kinedu\PaymentGateways\ApiResource;

class Card extends ApiResource
{
    /** @var string */
    const OBJECT_NAME = 'card';

    /**
     * Store a new credit card for the specified customer.
     *
     * @param  string  $customerId
     * @param  string  $cardToken
     *
     * @throws \Exception
     *
     * @return \Kinedu\PaymentGateways\Stripe\Card
     */
    public static function create(string $customerId, string $cardToken)
    {
        try {
            $newCard = StripeCustomer::createSource($customerId, [
                'source' => $cardToken,
            ]);
        } catch (Exception $e) {
            throw $e;
        }

        return Util::convertToObject($newCard);
    }
}

