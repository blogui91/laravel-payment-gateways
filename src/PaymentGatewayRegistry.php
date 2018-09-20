<?php

namespace Kinedu\PaymentGateways;

class PaymentGatewayRegistry
{
    /**
     * A list of supported payment gateways.
     *
     * @var array
     */
    protected $supportedGateways = [
        'SrPago',
    ];

    /**
     * Registered payment gateways.
     *
     * @var array
     */
    protected $registeredGateways = [];

    /**
     * Add a payment gateway provider to the listed registry.
     *
     * @param  string  $name
     * @param  \Kinedu\PaymentGateways\PaymentGateway  $instance
     * @return \Kinedu\PaymentGateways\PaymentGatewayRegistry
     */
    public function register(string $name, PaymentGateway $instance)
    {
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
