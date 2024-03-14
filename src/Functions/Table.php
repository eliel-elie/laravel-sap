<?php

namespace Elielelie\Sap\Functions;

use Carbon\Carbon;
use Elielelie\Sap\Connectors\Connection;
use Elielelie\Sap\Exceptions\FunctionModuleParameterBindException;
use Elielelie\Sap\Facades\Arr;
use Illuminate\Support\Collection;

class Table extends FunctionModule implements TableInterface
{
    /**
     * @var array
     */
    protected array $parameters = [
        'DELIMITER'   => "\x08",
        'QUERY_TABLE' => '',
        'FIELDS'      => [],
        'OPTIONS'     => [],
    ];

    /**
     * QueryBuilders
     *
     * @var QueryBuilder
     */
    public QueryBuilder $query;

    private array $attributes;

    /**
     * Create a new instance of RfcReadTable.
     *
     * @param Connection $handle
     *
     * @return void
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'RFC_READ_TABLE');
        $this->query = new QueryBuilder($this);
    }

    /**
     * Delimiter used by SAP to concatenate table rows
     *
     * @param string $value
     *
     * @return $this
     */
    public function delimiter(string $value): Table
    {
        return $this->param('DELIMITER', $value);
    }

    /**
     * Return query fields array.
     *
     * @param array $fields
     *
     * @return $this
     */
    public function fields(array $fields): Table
    {
        foreach ($fields as $key => $field) {
            $this->attributes[] = ['FIELDNAME' => strtoupper($field)];
            unset($fields[$key]);
        }
        return $this;
    }

    /**
     * Set fields for retrieval and execute function. Keep in mind this value is limited to
     * 512 bytes per row.
     *
     * @return Collection
     * @throws FunctionModuleParameterBindException
     */
    public function get(): Collection
    {
        $this->param('FIELDS', $this->attributes);
        $this->param('OPTIONS', $this->query->options());
        return $this->parse($this->execute());

    }

    /**
     * Limit table rows to provided number.
     *
     * @param int $number
     *
     * @return $this
     */
    public function limit(int $number)
    {
        return $this->param('ROWCOUNT', (int)$number);
    }

    /**
     * Skip provided number of rows from the result.
     *
     * @param int $number
     *
     * @return $this
     */
    public function offset(int $number)
    {
        return $this->param('ROWSKIPS', (int)$number);
    }

    /**
     * Set table to be queried.
     *
     * @param string $name
     *
     * @return $this
     */
    public function table(string $name): Table
    {
        return $this->param('QUERY_TABLE', strtoupper($name));
    }

    /**
     * Parse output from SAP and transform to Collection
     *
     * @param array $result
     *
     * @return Collection
     */
    public function parse(array $result): Collection
    {
        // Clear all that spaces.
        $result = Arr::trim($result);

        // Get DATA and FIELDS SAP tables.
        $data   = collect($result['DATA']);
        $fields = collect($result['FIELDS']);

        // Get columns.
        $columns = $fields->pluck('FIELDNAME')->toArray();

        // If no raw rows early exit.
        if ($data->count() === 0) {
            return collect();
        }

        // Explode raw data rows and combine with columns.
        $table = $data->pluck('WA')->transform(function($item) use ($columns)
        {
            $values = Arr::trim(explode($this->parameters['DELIMITER'], $item));
            return array_combine($columns, $values);
        });

        // Apply transformations in corelation with fields type.
        $fields->each(function ($field) use ($table) {
            // Transform dates.
            if ($field['TYPE'] === 'D') {
                $table->transform(function ($row) use($field) {
                    if ($row[$field['FIELDNAME']] == '00000000') {
                        $row[$field['FIELDNAME']] = null;
                    } else {
                        try {
                            $row[$field['FIELDNAME']] = Carbon::createFromFormat('Ymd', $row[$field['FIELDNAME']]);
                        } catch (\InvalidArgumentException $e) {
                            $row[$field['FIELDNAME']] = null;
                        }
                    }
                    return $row;
                });
            }
        });

        return $table;
    }

    /**
     * Dynamically handle calls to object methods.
     *
     * @param  string $method
     * @param  array  $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            return $this->{$method}(...$arguments);
        } elseif (method_exists($this->query, $method)) {
            return $this->query->{$method}(...$arguments);
        } else {
            trigger_error("Call to undefined method ". get_class($this) ."::$method()", E_USER_ERROR);
        }
    }
}
