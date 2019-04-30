<?php

namespace Kinedu\PaymentGateways\SrPago;

use DateTime;
use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use SrPago\{
    CustomerCards as SrPagoCustomerCards,
    Error\SrPagoError
};

class Card extends ApiResource
{
    const OBJECT_NAME = 'card';

    /**
     * Retrieve a listing of all credit cards for the specified customer.
     *
     * @param  string  $customerId
     * @return \Kinedu\PaymentGateways\SrPago\Card
     *
     * @throws \SrPago\Error\SrPagoError|\Exception
     */
    public static function all(string $customerId)
    {
        try {
            $cards = (new SrPagoCustomerCards())->all($customerId);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        $response = [
            'object' => 'list',
            'data' => Util::formatData($cards, static::OBJECT_NAME),
        ];

        return static::convertToObject($response);
    }

    /**
     * Store a new credit card for the specified customer.
     *
     * @param  string  $customerId
     * @param  string  $cardToken
     * @return \Kinedu\PaymentGateways\SrPago\Card
     *
     * @throws \SrPago\Error\SrPagoError|\Exception
     */
    public static function create(string $customerId, string $cardToken)
    {
        try {
            $newCard = (new SrPagoCustomerCards())->add($customerId, $cardToken);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        return static::convertToObject(static::formatCard($newCard));
    }

    /**
     * Charge the card for the specified amount.
     *
     * @param  int  $amount
     * @param  array  $options
     * @return \Kinedu\PaymentGateways\SrPago\Card
     */
    public function charge(int $amount, array $options = [])
    {
        $options = array_merge($options, [
            'card_token' => $this->token,
        ]);

        return Charge::create($amount, $options);
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
        [$expYear, $expMonth] = str_split($card['expiration'], 2);

        $formattedCard = [
            'token' => $card['token'],
            'card_brand' => $card['type'],
            'last_four' => substr($card['number'], -4),
            'exp_month' => $expMonth,
            'exp_year' => DateTime::createFromFormat('y', $expYear)->format('Y'),
        ];

        return $formattedCard;
    }
}
