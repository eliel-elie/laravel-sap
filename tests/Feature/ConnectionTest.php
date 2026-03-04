<?php

use Elielelie\Sap\Connectors\Connection;
use Elielelie\Sap\Connectors\Server;

describe('Connection', function () {
    $config = [
        'user'   => 'testuser',
        'passwd' => 'testpass',
        'client' => '100',
        'ashost' => 'localhost',
        'sysnr'  => '00',
        'lang'   => 'EN',
    ];

    describe('constructor', function () use ($config) {
        it('throws error when SAPNWRFC extension is not loaded', function () use ($config) {
            if (extension_loaded('sapnwrfc')) {
                $this->markTestSkipped('SAPNWRFC extension is loaded');
            }

            $server = new Server($config);

            expect(fn () => new Connection($server))
                ->toThrow(Error::class);
        });
    });

    describe('with mocked SAPNWRFC', function () {
        beforeEach(function () {
            if (! extension_loaded('sapnwrfc')) {
                $this->markTestSkipped('SAPNWRFC extension is not loaded - mock tests only');
            }
        });

        it('creates connection successfully', function () {
            $this->markTestSkipped('Requires real SAP connection or advanced mocking');
        });

        it('pings connection', function () {
            $this->markTestSkipped('Requires real SAP connection or advanced mocking');
        });

        it('closes connection', function () {
            $this->markTestSkipped('Requires real SAP connection or advanced mocking');
        });

        it('creates function module', function () {
            $this->markTestSkipped('Requires real SAP connection or advanced mocking');
        });

        it('creates custom function module', function () {
            $this->markTestSkipped('Requires real SAP connection or advanced mocking');
        });
    });
});
