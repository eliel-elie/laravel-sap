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
     *
     * @var Server
     */
    private Server $server;

    /**
     * Create a new instance of the object.
     *
     * @param Server $server
     * @return void
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->initialize();
    }

    /**
     * Check if the connection is alive.
     *
     * @return bool
     */
    public function ping()
    {
        return $this->handle->ping();
    }

    /**
     * Close the connection.
     *
     * @return bool True if the connection was closed, false if the connection
     *              is closed already.
     */
    public function close()
    {
        return $this->handle->close();
    }

    /**
     * Lookup a RFC function.
     *
     * @param string $name
     * @return FunctionModule
     */
    public function fm($name)
    {
        return new FunctionModule($this, $name);
    }

    /**
     * Lookup a custom RFC function.
     *
     * @param string $class
     * @return mixed
     */
    public function fmc($class)
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
     *
     * @return void
     */
    private function initialize()
    {
        try {

            $this->handle = new \SAPNWRFC\Connection($this->server->toArray());

        }
        catch (Exception $e) {
            throw new ConnectionException($e);
        }
        catch (RuntimeException $e) {
            throw new ConnectionException($e);
        }
    }
}
