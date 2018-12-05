<?php

namespace Kinedu\PaymentGateways;

interface PaymentGateway
{
    /**
     * Return a collection of customers from the payment provider.
     *
     * @param  array  $params
     * @return mixed  A collection of customers
     */
    public function getAllCustomers(array $params = []);

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
     * Return a listing of all cards belonging to the specified customer
     * from the payment provider's API.
     *
     * @param  string  $customerId
     * @return mixed
     */
    public function getAllCards(string $customerId);

    /**
     * Return a collection of all charges made to the customer.
     *
     * @param  array  $params
     * @return mixed
     */
    public function getAllCharges(array $params = []);

    /**
     * Charge the customer the given amount.
     *
     * @param  float  $amount
     * @param  array  $options
     * @return mixed
     */
    public function charge(float $amount, array $options = []);

    /**
     * Return the operation with the given transaction ID.
     *
     * @param  string  $transactionId
     * @return mixed
     */
    public function getOperation(string $transactionId);

    /**
     * Return the name of the current payment provider.
     *
     * @return string
     */
    public function getProviderName(): string;
}
