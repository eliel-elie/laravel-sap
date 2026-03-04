<?php

namespace Elielelie\Sap\Connectors;

use Exception;

class Server
{
    protected array $config;

    /**
     * Required config parameters.
     */
    private array $required = [
        'user', 'passwd', 'client', 'ashost', 'sysnr', 'lang',
    ];

    /**
     * Create a new instance of the Server object.
     *
     *
     * @return void
     */
    public function __construct(array $config)
    {
        if (array_diff($this->required, array_keys($config)) != []) {
            throw new Exception('Missing required attributes');
        }

        $this->config = $config;
    }

    public function toArray(): array
    {
        return $this->config;
    }
}
