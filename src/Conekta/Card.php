<?php

namespace Kinedu\PaymentGateways\Conekta;

use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use Conekta\{
    Customer as ConektaCustomer,
    CustomerCards as ConektaCustomerCards,
    Order as ConektaOrder,
    Handler as ConektaError
};

class Card extends ApiResource
{
    const OBJECT_NAME = 'card';

    /**
     * Retrieve a listing of all credit cards for the specified customer.
     *
     * @param  string  $customerId
     * @return \Kinedu\PaymentGateways\Conekta\Card
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function all(string $customerId)
    {
        try {
            $cards = ConektaCustomerCards::all($customerId);
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        $response = [
            'object' => 'list',
            'data' => Util::formatData($cards, static::OBJECT_NAME),
        ];

        return static::convertToObject((array) $response);
    }

    /**
     * Store a new credit card for the specified customer.
     *
     * @param  string  $customerId
     * @param  string  $cardToken
     * @return \Kinedu\PaymentGateways\Conekta\Card
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function create(string $customerId, string $cardToken)
    {
        try {
            $customer = ConektaCustomer::find($customerId);
            $newCard = $customer->createPaymentSource([
                'token_id' => $cardToken,
                'type' => 'card',
            ]);
            $newCard['token'] = $cardToken;
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject(static::formatCard((array) $newCard));
    }

    /**
     * Delete a credit card for the specified customer.
     *
     * @param  string  $customerId
     * @param  string  $cardToken
     * @return bool
     *
     * @throws \Conekta\Handler|\Exception
     */
    public static function delete(string $customerId, string $cardToken)
    {
        try {
            $customer = ConektaCustomer::find($customerId);

            foreach ($customer->payment_sources as $key => $card) {
                if ($card->id == $cardToken) {
                    $card->delete();
                    break;
                }
            }
        } catch (ConektaError $e) {
            throw new Exception($e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }

        return true;
    }

    /**
     * Charge the card for the specified amount.
     *
     * @param  int  $amount
     * @param  string  $customerId
     * @return \Kinedu\PaymentGateways\Conekta\Card
     */
    public function charge(int $amount, string $customerId)
    {
        $order = ConektaOrder::create([
            'currency' => 'MXN',
            'customer_info' => [
                'customer_id' => $customerId,
            ],
            'line_items' => [
                [
                    'name' => 'Servicios de Guardería',
                    'unit_price' => $amount,
                    'quantity' => 1,
                ],
            ],
            'charges' => [
                [
                    'payment_method' => [
                        'type' => 'card',
                    ],
                    'amount' => $amount,
                ],
            ],
        ]);

        return $order;
    }

    /**
     * Convert the customer array to an object.
     *
     * @param  array  $customer
     * @return array
     */
    private static function convertToObject(array $customer)
    {
        return Util::convertToSrPagoObject($customer);
    }

    /**
     * Format the given card to a normalized data structure.
     *
     * @param  array  $card
     * @return array
     */
    private static function formatCard(array $card): array
    {
        $formattedCard = [
            'payment_source_id' => $card['id'],
            'token' => $card['token'],
            'card_brand' => $card['brand'],
            'last_four' => $card['last4'],
            'exp_month' => $card['exp_month'],
            'exp_year' => $card['exp_year'],
        ];

        return $formattedCard;
    }
}
