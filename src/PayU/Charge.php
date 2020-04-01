<?php

namespace Kinedu\PaymentGateways\PayU;

use Exception;
use InvalidArgumentException;
use Kinedu\PaymentGateways\{
    ApiResource,
    PayU\Card as PayUCard,
    Util\Util
};
use PayU\{
    PayUCountries,
    PayUParameters,
    PayUPayments
};

class Charge extends ApiResource
{
    const OBJECT_NAME = 'payu_charge';

    /**
     * Attempt to charge the specified card.
     *
     * @param  float  $amount
     * @param  array  $options
     * @return \Kinedu\PaymentGateways\PayU\Charge
     *
     * @throws \InvalidArgumentException|\Exception
     */
    public static function create(float $amount, array $options = [])
    {
        if (! array_key_exists('card_token', $options)) {
            throw new InvalidArgumentException('A card token is required to know which card to charge.');
        } elseif (! array_key_exists('payu_account_id', $options)) {
            throw new InvalidArgumentException('A PayU account ID must be supplied.');
        }

        $cardId = $options['card_token'];

        if (! $card = PayUCard::find($cardId)) {
            throw new Exception('Card not found.');
        }

        $payload = [
            PayUParameters::ACCOUNT_ID => $options['payu_account_id'],
            PayUParameters::REFERENCE_CODE => $options['student_account_id'].'-'.date('Y/m/d H:i:s'),
            PayUParameters::DESCRIPTION => $options['student_account_id'],

            PayUParameters::VALUE => number_format($amount, 2, '.', ''),
            PayUParameters::TAX_VALUE => '0',
            PayUParameters::TAX_RETURN_BASE => '0',
            PayUParameters::CURRENCY => 'COP',

            PayUParameters::BUYER_NAME => $options['name'],
            PayUParameters::BUYER_EMAIL => $options['email'],
            PayUParameters::BUYER_CONTACT_PHONE => $options['phone'] ? $options['phone'] : '',
            PayUParameters::BUYER_COUNTRY => 'CO',
            PayUParameters::BUYER_PHONE => $options['phone'] ? $options['phone'] : '',

            PayUParameters::PAYER_NAME => $card->name,
            PayUParameters::PAYER_EMAIL => $options['email'],
            PayUParameters::PAYER_CONTACT_PHONE => $options['phone'] ? $options['phone'] : '',
            PayUParameters::PAYER_COUNTRY => 'CO',
            PayUParameters::PAYER_PHONE => $options['phone'] ? $options['phone'] : '',

            PayUParameters::TOKEN_ID => $cardId,

            PayUParameters::PAYMENT_METHOD => strtoupper($card->card_brand),

            PayUParameters::INSTALLMENTS_NUMBER => '1',
            PayUParameters::COUNTRY => PayUCountries::CO,

            // PayUParameters::NOTIFY_URL => base_url('tutores/listener'), // webhook URL

            PayUParameters::DEVICE_SESSION_ID => $options['session_id'],
            PayUParameters::IP_ADDRESS => $options['ip_address'],
            PayUParameters::PAYER_COOKIE => md5(date('Y-m-d H:i:s')),
            PayUParameters::USER_AGENT => $options['user_agent'],
        ];

        try {
            $charge = PayUPayments::doAuthorizationAndCapture($payload);

            $transaction = $charge->transactionResponse;

            if ($transaction->state === 'APPROVED') {
                // Respond with the successful charge
            } elseif ($transaction->state === 'PENDING') {
                // Respond with the charge, and maybe a message that the payment is still processing?
            } else {
                throw new Exception(
                    "Tarjeta de {$options['name']} con terminación {$card->last_four} "
                    ."rechazada por PayU. Error: '{$transaction->responseCode}'"
                );
            }
        } catch (Exception $e) {
            \Log::error($e);
            throw $e;
        }

        return static::convertToObject($transaction);
    }

    /**
     * Convert the charge array to an object.
     *
     * @param  array|object  $charge
     * @return \Kinedu\PaymentGateways\PayU\Charge
     */
    private static function convertToObject($charge)
    {
        return Util::convertToObject(static::normalizeCharge($charge));
    }

    /**
     * Normalize the charge data.
     *
     * @param  array|object  $charge
     * @return array
     */
    private static function normalizeCharge($charge): array
    {
        $charge = (array) $charge;

        return [
            'order_id' => $charge['orderId'],
            'transaction' => $charge['transactionId'],
            'status' => $charge['responseCode'],
            'timestamp' => $charge['operationDate'],
        ];
    }
}
