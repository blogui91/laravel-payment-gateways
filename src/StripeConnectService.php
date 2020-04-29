<?php

namespace Kinedu\PaymentGateways;

use Kinedu\PaymentGateways\Stripe\Connect\{
    Account,
    Person,
    Transfer
};

class StripeConnectService implements PaymentGatewayService
{
    /**
     * Create a new connect account.
     *
     * @param  array  $data
     *
     * @return \Kinedu\PaymentGateways\Stripe\Connect\Account
     */
    public function createAccount(array $data)
    {
        return Account::create($data);
    }

    /**
     * Return the account for the given ID.
     *
     * @param  string  $accountId
     *
     * @return \Kinedu\PaymentGateways\Stripe\Connect\Account
     */
    public function getAccount(string $accountId)
    {
        return Account::find($accountId);
    }

    /**
     * Update the given account ID.
     *
     * @param  string  $accountId
     * @param  array  $data
     *
     * @return \Kinedu\PaymentGateways\Stripe\Connect\Account
     */
    public function updateAccount(string $accountId, array $data)
    {
        return Account::find($accountId, $data);
    }

    /**
     * Create a new person for the given account.
     *
     * @param  string  $accountId
     * @param  array  $data
     *
     * @return \Kinedu\PaymentGateways\Stripe\Connect\Account
     */
    public function createPerson(string $accountId, array $data)
    {
        return Person::create($accountId, $data);
    }

    /**
     * Create a new transfer.
     *
     * @param  array  $data
     *
     * @return \Kinedu\PaymentGateways\Stripe\Connect\Transfer
     */
    public function createTransfer(array $data)
    {
        return Transfer::create($data);
    }
}
