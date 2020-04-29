<?php

namespace Kinedu\PaymentGateways;

use Exception;
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
     * Registered payment gateways services.
     *
     * @var array
     */
    protected $registeredServices = [
        //
    ];

    /**
     * Add a payment gateway provider to the listed registry.
     *
     * @param  string  $name
     * @param  \Kinedu\PaymentGateways\PaymentGateway|\Kinedu\PaymentGateways\PaymentGatewayService  $instance
     * @param  string  $serviceName
     *
     * @throws \InvalidArgumentException
     *
     * @return \Kinedu\PaymentGateways\PaymentGatewayRegistry
     */
    public function register(string $name, $instance, string $serviceName = null)
    {
        if (! $instance instanceof PaymentGateway &&
            ! $instance instanceof PaymentGatewayService) {
            throw new InvalidArgumentException("The instance needs to be PaymentGateway or PaymentGatewayService.");
        }

        $supportedGateways = $this->supportedGateways;

        $gateway = Arr::first($supportedGateways, function ($value) use ($name) {
            $name = explode(" ", $name);
            $name = reset($name);

            return Str::startsWith($value, $name);
        });

        if (! $gateway) {
            throw new InvalidArgumentException('The given gateway is not currently supported.');
        }

        $isService = $serviceName;

        if (! $isService) {
            $this->registeredGateways[$name] = $instance;

            return $this;
        }

        if (! $services = Arr::get($this->registeredServices, $name)) {
            $this->registeredServices[$name] = [];
        }

        $this->registeredServices[$name][$serviceName] = $instance;

        return $this;
    }

    /**
     * Add a payment gateway provider service to the listed registry.
     *
     * @param  string  $name
     * @param  \Kinedu\PaymentGateways\PaymentGateway|\Kinedu\PaymentGateways\PaymentGatewayService  $instance
     * @param  string  $serviceName
     *
     * @return \Kinedu\PaymentGateways\PaymentGatewayRegistry
     */
    public function registerService(string $name, $instance, string $serviceName)
    {
        return $this->register($name, $instance, $serviceName);
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
        $registeredGateways = $this->registeredGateways;

        if (! $gateway = Arr::get($registeredGateways, $name)) {
            throw new Exception('Invalid gateway');
        }

        $registeredServices = $this->registeredServices;

        if (($services = Arr::get($registeredServices, $name)) && $gateway->supportServices) {
            $gateway->attachServices($services);
        }

        return $gateway;
    }
}
