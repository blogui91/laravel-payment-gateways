<?php

namespace Kinedu\PaymentGateways\SrPago;

use Exception;
use Kinedu\PaymentGateways\{
    ApiResource,
    Util\Util
};
use SrPago\{
    Operations as SrPagoOperation,
    Error\SrPagoError
};

class Operation extends ApiResource
{
    const OBJECT_NAME = 'operation';

    /**
     * Retrieve a listing of all SrPago operations.
     *
     * @param  array  $parameters
     * @return \Kinedu\PaymentGateways\SrPago\Collection
     */
    public static function all($parameters = [])
    {
        try {
            $operations = (new SrPagoOperation())->all($parameters);
        } catch (SrPagoError $e) {
            throw new Exception($e->getError()['message']);
        } catch (Exception $e) {
            throw $e;
        }

        $response = array_merge($operations, [
            'object' => 'list',
            'data' => Util::formatData($operations['operations'], static::OBJECT_NAME),
        ]);
        unset($response['operations']);

        return static::convertToObject($response);
    }

    /**
     * Retrieve the operation with the matching transaction ID.
     *
     * @param  string  $transactionId
     * @return \Kinedu\PaymentGateways\SrPago\Operation
     */
    public static function find(string $transactionId)
    {
        $operation = (new SrPagoOperation())->retreive($transactionId);

        return static::convertToObject(
            Util::formatData($operation, static::OBJECT_NAME)
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
