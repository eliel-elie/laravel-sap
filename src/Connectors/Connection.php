<?php

namespace Elielelie\Sap\Connectors;

use Elielelie\Sap\Exceptions\ConnectionException;
use Elielelie\Sap\Functions\FunctionModule;
use Exception;
use RuntimeException;

class Connection
{
    /**
     * Sapnwrfc handle.
     *
     * @var SAPNWRFC\Connection
     */
    private $handle;

    /**
     * Server config.
     */
    private Server $server;

    /**
     * Create a new instance of the object.
     *
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->initialize();
    }

    /**
     * Check if the connection is alive.
     */
    public function ping(): bool
    {
        return $this->handle->ping();
    }

    /**
     * Close the connection.
     *
     * @return bool True if the connection was closed, false if the connection
     *              is closed already.
     */
    public function close(): bool
    {
        return $this->handle->close();
    }

    /**
     * Lookup a RFC function.
     */
    public function fm(string $name): FunctionModule
    {
        return new FunctionModule($this, $name);
    }

    /**
     * Lookup a custom RFC function.
     *
     * @return mixed
     */
    public function fmc(string $class)
    {
        return new $class($this);
    }

    /**
     * Retrieve the connection handle.
     *
     * @return SAPNWRFC\Connection
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * Perform the actual connection.
     */
    private function initialize(): void
    {
        try {
            $this->handle = new \SAPNWRFC\Connection($this->server->toArray());
        } catch (Exception $e) {
            throw new ConnectionException($e);
        } catch (RuntimeException $e) {
            throw new ConnectionException($e);
        }
    }
}
