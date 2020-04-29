<?php

namespace Kinedu\PaymentGateways\Stripe\Connect;

use Exception;
use Stripe\Account as StripeAccount;
use Kinedu\PaymentGateways\Util\Util;
use Kinedu\PaymentGateways\ApiResource;

class Person extends ApiResource
{
    /** @var string */
    const OBJECT_NAME = 'person';

    /**
     * Create a new person for the given account.
     *
     * @param  string  $accountId
     * @param  array  $data
     *
     * @throws \Exception
     *
     * @return \Kinedu\PaymentGateways\Stripe\Connect\Person
     */
    public function create(string $accountId, array $data)
    {
        try {
            $person = StripeAccount::createPerson($accountId, $data);
        } catch (Exception $e) {
            throw $e;
        }

        return Util::convertToObject($person);
    }
}
