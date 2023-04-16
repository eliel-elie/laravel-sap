<?php

namespace Elielelie\Sap\Functions;

use Elielelie\Sap\Connectors\Connection;
use Elielelie\Sap\Exceptions\FunctionModuleParameterBindException;
use Elielelie\Sap\Facades\Arr;
use Illuminate\Support\Collection;

class LongText extends FunctionModule
{
    /**
     * @var array
     */
    protected array $parameters = [
        'TEXT_LINES' => [],
    ];

    /**
     * @var array
     */
    public array $query = [];

    /**
     * @var string
     */
    protected string $type = 'LTXT';

    /**
     * @var array|string[]
     */
    protected array $languages = ['P', 'E'];

    private string $table;

    /**
     * Create a new instance of RfcReadTable.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'RFC_READ_TEXT');
    }

    /**
     * Set table to be queried.
     *
     * @param string $name
     *
     * @return $this
     */
    public function table(string $name): LongText
    {
        $this->table = strtoupper($name);
        return $this;
    }

    /**
     * Set table to be queried.
     *
     * @param array $values
     * @return $this
     */
    public function where(array $values): LongText
    {
        foreach ($values as $value) {
            foreach ($this->languages as $language) {
                $this->query[] = [
                    'TDOBJECT' => $this->table,
                    'MANDT'    => '100',
                    'TDNAME'   => $value,
                    'TDID'     => $this->type,
                    'TDSPRAS'  => $language
                ];
            }
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
        $this->param('TEXT_LINES', $this->query);
        return $this->parse($this->execute());
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
        $result = Arr::trim($result);
        $data   = collect($result['TEXT_LINES']);

        if ($data->count() === 0) return collect();

        return $data->map(function ($item) {
            return collect($item)->only(['TDNAME', 'TDLINE']);
        })->filter(function ($item){
            return strlen(trim($item['TDLINE'])) > 0 ?? $item;
        });
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
