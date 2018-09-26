<?php

namespace Kinedu\PaymentGateways;

class SrPagoObject implements \ArrayAccess, \Countable, \JsonSerializable
{
    /** @var array */
    protected $values;

    public function __construct($id = null)
    {
        $this->values = [];

        if (! is_null($id)) {
            $this->values['id'] = $id;
        }
    }

    public function __set($key, $value)
    {
        $this->values[$key] = Util\Util::convertToSrPagoObject($value);
    }

    public function __get($property)
    {
        if (! empty($this->values) && array_key_exists($property, $this->values)) {
            return $this->values[$property];
        }
    }

    public function  __isset($property)
    {
        return isset($this->values[$property]);
    }

    /**
     * This unfortunately needs to be public to be used in Util\Util.
     *
     * @param array $values
     *
     * @return \Kinedu\PaymentGateways\SrPagoObject  The object constructed from the given values.
     */
    public static function constructFrom($values)
    {
        $obj = new static(isset($values['id']) ? $values['id'] : null);
        $obj->refreshFrom($values);

        return $obj;
    }

    /**
     * Refreshes this object using the provided values.
     *
     * @param array $values
     */
    public function refreshFrom($values)
    {
        if ($values instanceof SrPagoObject) {
            $values = $values->__toArray(true);
        }

        $this->updateAttributes($values);
    }

    /**
     * Mass assigns attributes on the model.
     *
     * @param array $values
     */
    public function updateAttributes($values)
    {
        foreach ($values as $key => $value) {
            // Special-case metadata to always be cast as a StripeObject
            // This is necessary in case metadata is empty, as PHP arrays do
            // not differentiate between lists and hashes, and we consider
            // empty arrays to be lists.
            if ($key === 'metadata') {
                $this->values[$key] = self::constructFrom($value);
            } else {
                $this->values[$key] = Util\Util::convertToSrPagoObject($value);
            }
        }
    }


    // ArrayAccess methods

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return array_key_exists($key, $this->values) ? $this->values[$key] : null;
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->$key = $value;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->$key);
    }

    // Countable method
    public function count()
    {
        return count($this->values);
    }

    public function keys()
    {
        return array_keys($this->values);
    }

    public function values()
    {
        return array_values($this->values);
    }

    // JsonSerializable method
    public function jsonSerialize()
    {
        return $this->__toArray(true);
    }

    public function __toJSON()
    {
        return json_encode($this->__toArray(true), JSON_PRETTY_PRINT);
    }

    /**
     * Convert the object to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        $class = get_class($this);
        return $class . ' JSON: ' . $this->__toJSON();
    }

    public function __toArray($recursive = false)
    {
        if ($recursive) {
            return Util\Util::convertSrPagoObjectToArray($this->values);
        } else {
            return $this->values;
        }
    }
}
