<?php

namespace Kinedu\PaymentGateways\PayU;

use DateTime;
use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use PayU\{
    PayUCreditCards,
    PayUParameters,
};

class Card extends ApiResource
{
    const OBJECT_NAME = 'payu_card';

    /**
     * Find the card with the specified token.
     *
     * @param  string  $token
     * @return \Kinedu\PaymentGateways\PayU\Card
     */
    public static function find(string $token)
    {
        try {
            $card = PayUCreditCards::find([
                PayUParameters::TOKEN_ID => $token,
            ]);
        } catch (Exception $e) {
            \Log::error($e);
            throw $e;
        }

        return static::convertToObject($card);
    }

    /**
     * Convert the card array to an object.
     *
     * @param  array|object  $card
     * @return \Kinedu\PaymentGateways\PayU\Card
     */
    private static function convertToObject($card)
    {
        return Util::convertToSrPagoObject(static::normalizeCard($card));
    }

    /**
     * Normalize the card data.
     *
     * @param  array|object  $card
     * @return array
     */
    private static function normalizeCard($card): array
    {
        $card = (array) $card;

        return [
            'name' => $card['name'],
            'token' => $card['token'],
            'card_brand' => strtolower($card['type']),
            'last_four' => substr($card['number'], -4),
            // Expiry month and year not available
        ];
    }
}
