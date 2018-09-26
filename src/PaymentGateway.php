<?php

namespace Kinedu\PaymentGateways;

interface PaymentGateway
{
    /**
     * Create a new customer in the payment provider's API.
     *
     * @param  array  $data  The customer information to be stored.
     * @return mixed
     */
    public function createCustomer(array $data);

    /**
     * Add a new customer card.
     *
     * @param  string  $customerId
     * @param  string  $token
     * @return mixed
     */
    public function addCard(string $customerId, string $token);

    /**
     * Return the name of the current payment provider.
     *
     * @return string
     */
    public function getProviderName(): string;
}
