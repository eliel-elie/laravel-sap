<?php

namespace Elielelie\Sap\Functions;

use Elielelie\Sap\Exceptions\FunctionModuleParameterBindException;
use Illuminate\Support\Collection;

interface TableInterface
{
    /**
     * Sets the delimiter used to concatenate table rows.
     *
     * @param string $value
     * @return $this
     */
    public function delimiter(string $value): self;

    /**
     * Sets the fields to be retrieved.
     *
     * @param array $fields
     * @return $this
     */
    public function fields(array $fields): self;

    /**
     * Executes the query and returns the results as a Collection.
     *
     * @return Collection
     * @throws FunctionModuleParameterBindException
     */
    public function get(): Collection;

    /**
     * Limits the number of rows returned.
     *
     * @param int $number
     * @return $this
     */
    public function limit(int $number): self;

    /**
     * Skips a specified number of rows in the result.
     *
     * @param int $number
     * @return $this
     */
    public function offset(int $number): self;

    /**
     * Sets the table to be queried.
     *
     * @param string $name
     * @return $this
     */
    public function table(string $name): self;
}
