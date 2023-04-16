<?php

namespace Elielelie\Sap\Functions;

use Closure;
use Elielelie\Sap\Connectors\Connection;
use Elielelie\Sap\Exceptions\FunctionCallException;
use Elielelie\Sap\Exceptions\FunctionModuleParameterBindException;
use Illuminate\Support\Collection;

class FunctionModule
{
    /**
     * Name of the function module.
     *
     * @var string
     */
    public $name;

    /**
     * Connection of the function module.
     *
     * @var Connection
     */
    public Connection $connection;

    /**
     * FunctionModule parameters
     *
     * @var array
     */
    protected array $parameters = [];

    /**
     * @var SAPNWRFC\RemoteFunction
     */
    protected $handle;

    /**
     * Description of the function module.
     *
     * @var Collection
     */
    protected Collection $description;
    /**
     * Create a new instance of the object.
     *
     * @param Connection $connection
     * @param string     $name
     * @return void
     */

    public function __construct(Connection $connection, $name)
    {
        $this->connection = $connection;
        $this->name       = $name;

        $this->initialize();
    }

    /**
     * Execute the function module.
     *
     * @return mixed
     */
    public function execute()
    {
        return $this->safe(function () {
            return $this->handle->invoke($this->parameters, ['rtrim' => true]);
        });
    }

    /**
     * Add a parameter to the function module.
     *
     * @param  string $name
     * @param  mixed $value
     * @return $this
     */
    public function param($name, $value)
    {

        // Perform parameter exists check.
        if (!$this->description->has($name)) {
            throw new FunctionModuleParameterBindException(
                sprintf(
                    'Function module parameter (%s) not found. Available parameters: %s.',
                    $name,
                    $this->description->keys()->implode(', ')
                )
            );
        }

        // Perform parameter type check.
        $type = $this->description[$name]['type'];
        $exception = false;

        if (($type === 'RFCTYPE_TABLE' || $type === 'RFCTYPE_STRUCTURE') && !is_array($value)) {
            $type .= ' (array)';
            $exception = true;
        } elseif ($type === 'RFCTYPE_CHAR' && !is_string($value)) {
            $type .= ' (string)';
            $exception = true;
        } elseif ($type === 'RFCTYPE_BYTE' && !is_string($value)) {
            $type .= ' (binary)';
            $exception = true;
        } elseif ($type === 'RFCTYPE_INT' && !is_int($value)) {
            $type .= ' (integer)';
            $exception = true;
        }

        if ($exception) {
            throw new FunctionModuleParameterBindException(
                sprintf(
                    'Function module parameter type mismatch. Needed %s, given %s',
                    $type,
                    gettype($value)
                )
            );
        }

        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Initialize the function module.
     *
     * @return void
     * @throws FunctionCallException
     */
    private function initialize()
    {
        $this->safe(function () {

            $this->handle = $this->connection
                ->getHandle()
                ->getFunction($this->name);

            // Save the description.
            $this->description = collect(
                json_decode(
                    json_encode($this->handle->getFunctionDescription()),true)
            )->except('name');

            if ($this->description->isEmpty() && !empty($this->parameters)) {
                $this->description = collect($this->parameters);
            }

        });
    }

    /**
     * Wrap a callable with mixed version exception handle.
     *
     * @param  callable $callback
     *
     * @return mixed
     */
    private function safe(callable $callback)
    {
        try {
            return $callback();
        }
        catch (\Exception $e) {
            throw new FunctionCallException($e);
        }
        catch (\RuntimeException $e) {
            throw new FunctionCallException($e);
        }
    }

    /**
     * Get FunctionModule description.
     *
     * @return Collection
     */
    public function description(): Collection
    {
        return $this->description;
    }
    
}
