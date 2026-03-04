<?php

namespace Elielelie\Sap\Helpers;

class Guid
{
    /**
     * Convert guid from bytes to char32.
     */
    public function byteToChar32(string $bytes): string
    {
        return strtoupper(unpack('H*', $bytes)[1]);
    }

    /**
     * Convert from char32 to byte.
     */
    public function char32ToByte(string $string): string
    {
        return pack('H*', $string);
    }
}
