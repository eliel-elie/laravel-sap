<?php

namespace Elielelie\Sap\Commands;

use Elielelie\Sap\Exceptions\ConnectionException;
use Elielelie\Sap\Sap;
use Exception;
use Illuminate\Console\Command;

class TestSapConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'sap:test {connection? : The name of the SAP connection to test.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the configured application SAP connections.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($connection = $this->argument('connection')) {
            $connections = [$connection => config('sap.connections.' . $connection)];
        } else {
            $connections = config('sap.connections');
        }

        if (empty($connections)) {
            $this->error('No SAP connections have been defined.');

            return Command::FAILURE;
        }

        $tested = [];

        foreach ($connections as $name => $connection) {
            $tested[] = $this->performTest($name, $connection);
        }

        $this->table(['Connection', 'Successful', 'Host', 'Username', 'Message', 'Response Time'], $tested);

        return Command::SUCCESS;
    }

    protected function performTest($name, array $connection): array
    {
        $this->info("Testing SAP connection [$name]...");

        $start  = microtime(true);

        $result = $this->attempt($name);

        return [
            $name,
            $result['connected'] ? '✔ Yes' : '✘ No',
            $connection['ashost'],
            $connection['user'],
            $result['message'],
            (app()->runningUnitTests() ? '0' : $this->getElapsedTime($start)) . 'ms',
        ];
    }

    protected function attempt(string $name): array
    {
        try {
            $sap        = app(Sap::class);
            $connection = $sap->open($name);

            $connected  = false;

            if ($connection->ping()) {
                $message   = 'Successfully connected.';
                $connected = true;
            } else {
                $message = 'Could not establish connection with SAP.';
            }
        } catch (Exception|ConnectionException $e) {
            $message   = sprintf(
                '%s. Error Code: [%s]',
                $e->getMessage(),
                $e->getCode()
            );

            $connected = false;
        }

        return [
            'message'   => $message,
            'connected' => $connected,
        ];
    }

    protected function getElapsedTime($start): float
    {
        return round((microtime(true) - $start) * 1000, 2);
    }
}
