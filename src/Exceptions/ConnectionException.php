<?php

namespace Elielelie\Sap\Exceptions;

use Exception;

class ConnectionException extends Exception
{
    /**
     * Create a new instance of the object.
     *
     * @param string $exception
     */
    public function __construct($exception)
    {
        parent::__construct($exception->getMessage());
    }
}