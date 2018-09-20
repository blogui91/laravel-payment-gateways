<?php

namespace Kinedu\PaymentGateways;

use Exception;
use SrPago\{
    Customer as SrPagoCustomer,
    CustomerCards as SrPagoCustomerCards,
    Error\SrPagoError,
    SrPago
};

class SrPagoPaymentGateway implements PaymentGateway
{
    public function __construct(string $apiKey, string $apiSecret, bool $livemode = false)
    {
        SrPago::setLiveMode($livemode);
        SrPago::setApiKey($apiKey);
        SrPago::setApiSecret($apiSecret);
    }

    /**
     * Create a new customer.
     *
     * @param  array  $data  The customer information to be stored.
     * @return array
     */
    public function createCustomer(array $data): array
    {
        try {
            $newCustomer = (new SrPagoCustomer())->create($data);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        }

        return [
            'id' => $newCustomer['id'],
        ];
    }

    /**
     * Add a new customer card.
     *
     * @param  string  $customerId
     * @param  string  $token
     * @return array
     */
    public function addCard(string $customerId, string $token): array
    {
        try {
            $newCard = (new SrPagoCustomerCards())->add($customerId, $token);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        }

        return [
            'token' => $newCard['token'],
            'card_brand' => $newCard['type'],
            'last_four' => substr($newCard['number'], -4),
            'exp_month' => null,
            'exp_year' => $newCard['expiration'],
        ];
    }

    /**
     * Return the name of the current payment provider.
     *
     * @return string
     */
    public function getProviderName(): string
    {
        return 'SrPago';
    }
}
