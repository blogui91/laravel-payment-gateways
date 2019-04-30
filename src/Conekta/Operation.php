<?php

namespace Kinedu\PaymentGateways\Conekta;

use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use Conekta\{
    Order as ConektaOrder,
    Handler as ConektaError
};

class Operation extends ApiResource
{
    const OBJECT_NAME = 'operation';

    /**
     * Retrieve the operation with the matching transaction ID.
     *
     * @param  string  $transactionId
     * @return \Kinedu\PaymentGateways\Conekta\Operation
     */
    public static function find(string $transactionId)
    {
        $operation = ConektaOrder::find($transactionId);

        return static::convertToObject(
            Util::formatData((array) $operation, static::OBJECT_NAME)
        );
    }

    /**
     * Convert the operation array to an object.
     *
     * @param  array  $operation
     * @return array
     */
    private static function convertToObject(array $operation)
    {
        return Util::convertToSrPagoObject($operation);
    }
}
