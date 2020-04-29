<?php

namespace Kinedu\PaymentGateways\Traits;

use InvalidArgumentException;

trait HasServices
{
    /** @var bool */
    public $supportServices = true;

    /** @var array */
    protected $services = [
        //
    ];

    /**
     * Return the service of the given name.
     *
     * @param  string  $name
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function service(string $name)
    {
        if (! isset($this->services[$name])) {
            throw new InvalidArgumentException("The service {$name} is not registered.");
        }

        return $this->services[$name];
    }

    /**
     * Attach the given services with the payment provider.
     *
     * @param  array  $services
     *
     * @return void
     */
    public function attachServices(array $services)
    {
        $this->services = $services;
    }
}
