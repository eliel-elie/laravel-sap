<?php

use Elielelie\Sap\Helpers\Guid;

describe('Guid Helper', function () {
    beforeEach(function () {
        $this->guid = new Guid;
    });

    describe('byteToChar32', function () {
        it('converts byte to char32 format', function () {
            $bytes  = pack('H*', 'a1b2c3d4e5f6');
            $result = $this->guid->byteToChar32($bytes);

            expect($result)
                ->toBeString()
                ->toBe('A1B2C3D4E5F6');
        });

        it('returns uppercase string', function () {
            $bytes  = pack('H*', 'abcdef');
            $result = $this->guid->byteToChar32($bytes);

            expect($result)->toBe(strtoupper('abcdef'));
        });

        it('handles 32 character guid', function () {
            $hex    = '0123456789ABCDEF0123456789ABCDEF';
            $bytes  = pack('H*', $hex);
            $result = $this->guid->byteToChar32($bytes);

            expect($result)
                ->toHaveLength(32)
                ->toBe($hex);
        });
    });

    describe('char32ToByte', function () {
        it('converts char32 to byte format', function () {
            $char32 = 'A1B2C3D4E5F6';
            $result = $this->guid->char32ToByte($char32);

            expect($result)->toBe(pack('H*', $char32));
        });

        it('is reversible with byteToChar32', function () {
            $original = '0123456789ABCDEF0123456789ABCDEF';
            $bytes    = $this->guid->char32ToByte($original);
            $result   = $this->guid->byteToChar32($bytes);

            expect($result)->toBe($original);
        });

        it('handles lowercase input', function () {
            $char32 = 'abcdef';
            $result = $this->guid->char32ToByte($char32);

            expect($result)->toBe(pack('H*', $char32));
        });
    });
});
