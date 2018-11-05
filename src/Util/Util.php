<?php

namespace Kinedu\PaymentGateways\Util;

use Kinedu\PaymentGateways\{
    SrPago\Collection,
    SrPagoObject
};

abstract class Util
{
    /**
     * Determine if the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     *
     * @return boolean true if the given object is a list.
     */
    public static function isList($array)
    {
        if (! is_array($array)) {
            return false;
        }

        if ($array === array()) {
            return true;
        }

        if (array_keys($array) !== range(0, count($array) - 1)) {
            return false;
        }

        return true;
    }

    /**
     * Recursively converts the PHP SrPago object to an array.
     *
     * @param  array  $values  The PHP SrPago object to convert.
     * @return array
     */
    public static function convertSrPagoObjectToArray($values)
    {
        $results = [];

        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if ($k[0] == '_') {
                continue;
            }

            if ($v instanceof SrPagoObject) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertSrPagoObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }

        return $results;
    }

    /**
     * Converts a response from the SrPago API to the corresponding PHP object.
     *
     * @param  array  $response The response from the SrPago API.
     * @param  string|null  $opts
     *
     * @return SrPagoObject|array
     */
    public static function convertToSrPagoObject($response)
    {
        $types = [
            // data structures
            \Kinedu\PaymentGateways\SrPago\Collection::OBJECT_NAME => 'Kinedu\\PaymentGateways\\SrPago\\Collection',

            // business objects
            \Kinedu\PaymentGateways\SrPago\Card::OBJECT_NAME => 'Kinedu\\PaymentGateways\\SrPago\\Card',
            \Kinedu\PaymentGateways\SrPago\Charge::OBJECT_NAME => 'Kinedu\\PaymentGateways\\SrPago\\Charge',
            \Kinedu\PaymentGateways\SrPago\Customer::OBJECT_NAME => 'Kinedu\\PaymentGateways\\SrPago\\Customer',
            \Kinedu\PaymentGateways\SrPago\Operation::OBJECT_NAME => 'Kinedu\\PaymentGateways\\SrPago\\Operation',
        ];

        if (self::isList($response)) {
            $mapped = [];

            foreach ($response as $i) {
                array_push($mapped, self::convertToSrPagoObject($i));
            }

            return $mapped;
        } elseif (is_array($response)) {
            if (isset($response['object'])
                && is_string($response['object'])
                && isset($types[$response['object']])
            ) {
                $class = $types[$response['object']];
            } else {
                $class = 'Kinedu\\PaymentGateways\\SrPagoObject';
            }

            return $class::constructFrom($response);
        }

        return $response;
    }

    /**
     * Format an API response ready to be converted to an object.
     *
     * @param  array  $data
     * @param  string  $objectName
     * @return array
     */
    public static function formatData($data, string $objectName = null)
    {
        if (self::isList($data)) {
            foreach ($data as $key => $object) {
                if (is_array($object)) {
                    $data[$key]['object'] = $objectName;
                }
            }
        } else {
            $data['object'] = $objectName;
        }

        return $data;
    }
}
