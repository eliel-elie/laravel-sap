<?php

use Elielelie\Sap\Connectors\Server;

describe('Server', function () {
    describe('constructor', function () {
        it('creates server with valid config', function () {
            $config = [
                'user'   => 'testuser',
                'passwd' => 'testpass',
                'client' => '100',
                'ashost' => 'localhost',
                'sysnr'  => '00',
                'lang'   => 'EN',
            ];

            $server = new Server($config);

            expect($server)->toBeInstanceOf(Server::class);
        });

        it('throws exception when missing required parameters', function () {
            $config = [
                'user'   => 'testuser',
                'passwd' => 'testpass',
            ];

            expect(fn () => new Server($config))
                ->toThrow(Exception::class, 'Missing required attributes');
        });

        it('throws exception when missing user', function () {
            $config = [
                'passwd' => 'testpass',
                'client' => '100',
                'ashost' => 'localhost',
                'sysnr'  => '00',
                'lang'   => 'EN',
            ];

            expect(fn () => new Server($config))
                ->toThrow(Exception::class);
        });

        it('throws exception when missing passwd', function () {
            $config = [
                'user'   => 'testuser',
                'client' => '100',
                'ashost' => 'localhost',
                'sysnr'  => '00',
                'lang'   => 'EN',
            ];

            expect(fn () => new Server($config))
                ->toThrow(Exception::class);
        });

        it('throws exception when missing client', function () {
            $config = [
                'user'   => 'testuser',
                'passwd' => 'testpass',
                'ashost' => 'localhost',
                'sysnr'  => '00',
                'lang'   => 'EN',
            ];

            expect(fn () => new Server($config))
                ->toThrow(Exception::class);
        });

        it('accepts additional optional parameters', function () {
            $config = [
                'user'   => 'testuser',
                'passwd' => 'testpass',
                'client' => '100',
                'ashost' => 'localhost',
                'sysnr'  => '00',
                'lang'   => 'EN',
                'trace'  => '1',
                'debug'  => true,
            ];

            $server = new Server($config);

            expect($server->toArray())->toHaveKey('trace')
                ->and($server->toArray())->toHaveKey('debug');
        });
    });

    describe('toArray', function () {
        it('returns config as array', function () {
            $config = [
                'user'   => 'testuser',
                'passwd' => 'testpass',
                'client' => '100',
                'ashost' => 'localhost',
                'sysnr'  => '00',
                'lang'   => 'EN',
            ];

            $server = new Server($config);

            expect($server->toArray())->toBe($config);
        });

        it('preserves all config values', function () {
            $config = [
                'user'      => 'SAP_USER',
                'passwd'    => 'S3cr3t!',
                'client'    => '800',
                'ashost'    => '192.168.1.100',
                'sysnr'     => '01',
                'lang'      => 'PT',
                'saprouter' => '/H/router.example.com/S/3299/H/',
            ];

            $server = new Server($config);
            $result = $server->toArray();

            expect($result['user'])->toBe('SAP_USER')
                ->and($result['passwd'])->toBe('S3cr3t!')
                ->and($result['client'])->toBe('800')
                ->and($result['ashost'])->toBe('192.168.1.100')
                ->and($result['sysnr'])->toBe('01')
                ->and($result['lang'])->toBe('PT')
                ->and($result['saprouter'])->toBe('/H/router.example.com/S/3299/H/');
        });
    });
});
