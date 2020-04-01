<?php

namespace Kinedu\PaymentGateways;

use InvalidArgumentException;
use Illuminate\Support\{
    Arr,
    Str
};

class PaymentGatewayRegistry
{
    /**
     * A list of supported payment gateways.
     *
     * @var array
     */
    protected $supportedGateways = [
        'SrPago',
        'Stripe',
    ];

    /**
     * Registered payment gateways.
     *
     * @var array
     */
    protected $registeredGateways = [
        //
    ];

    /**
     * Add a payment gateway provider to the listed registry.
     *
     * @param  string  $name
     * @param  \Kinedu\PaymentGateways\PaymentGateway  $instance
     *
     * @throws \InvalidArgumentException
     *
     * @return \Kinedu\PaymentGateways\PaymentGatewayRegistry
     */
    public function register(string $name, PaymentGateway $instance)
    {
        $supportedGateways = $this->supportedGateways;

        $gateway = Arr::first($supportedGateways, function ($value) use ($name) {
            $name = explode(" ", $name);
            $name = reset($name);

            return Str::startsWith($value, $name);
        });

        if (! $gateway) {
            throw new InvalidArgumentException('The given gateway is not currently supported.');
        }

        $this->registeredGateways[$name] = $instance;

        return $this;
    }

    /**
     * Return the payment gateway matching the specified name.
     *
     * @param  string  $name
     * @throws \Exception
     * @return \Kinedu\PaymentGateways\PaymentGateway
     */
    public function get(string $name)
    {
        if (array_key_exists($name, $this->registeredGateways)) {
            return $this->registeredGateways[$name];
        } else {
            throw new \Exception('Invalid gateway');
        }
    }
}
