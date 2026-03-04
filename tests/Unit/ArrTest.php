<?php

use Elielelie\Sap\Helpers\Arr;

describe('Arr Helper', function () {
    beforeEach(function () {
        $this->arr = new Arr;
    });

    describe('trim', function () {
        it('trims a simple string', function () {
            $result = $this->arr->trim('  hello  ');

            expect($result)->toBe('hello');
        });

        it('trims all strings in array', function () {
            $input  = ['  hello  ', '  world  '];
            $result = $this->arr->trim($input);

            expect($result)->toBe(['hello', 'world']);
        });

        it('trims nested arrays recursively', function () {
            $input  = [
                '  level1  ',
                ['  level2  ', '  nested  '],
                ['deep' => ['  level3  ']],
            ];

            $result = $this->arr->trim($input);

            expect($result)->toBe([
                'level1',
                ['level2', 'nested'],
                ['deep' => ['level3']],
            ]);
        });

        it('handles empty arrays', function () {
            $result = $this->arr->trim([]);

            expect($result)->toBe([]);
        });

        it('handles mixed types in arrays', function () {
            $input  = ['  text  ', '  123  ', '  true  '];
            $result = $this->arr->trim($input);

            expect($result[0])->toBe('text')
                ->and($result[1])->toBe('123')
                ->and($result[2])->toBe('true');
        });
    });

    describe('utf8ize', function () {
        it('converts ISO-8859-1 string to UTF-8', function () {
            $iso    = mb_convert_encoding('café', 'ISO-8859-1', 'UTF-8');
            $result = $this->arr->utf8ize($iso);

            expect($result)->toBe('café');
        });

        it('converts all strings in array', function () {
            $iso1   = mb_convert_encoding('café', 'ISO-8859-1', 'UTF-8');
            $iso2   = mb_convert_encoding('naïve', 'ISO-8859-1', 'UTF-8');

            $input  = [$iso1, $iso2];
            $result = $this->arr->utf8ize($input);

            expect($result)->toBe(['café', 'naïve']);
        });

        it('handles nested arrays recursively', function () {
            $iso    = mb_convert_encoding('über', 'ISO-8859-1', 'UTF-8');
            $input  = [
                'level1' => [$iso],
                'level2' => ['nested' => [$iso]],
            ];

            $result = $this->arr->utf8ize($input);

            expect($result['level1'][0])->toBe('über')
                ->and($result['level2']['nested'][0])->toBe('über');
        });

        it('returns non-string values unchanged in array', function () {
            $input  = ['text', 'another'];
            $result = $this->arr->utf8ize($input);

            expect($result)->toBeArray()
                ->and(count($result))->toBe(2);
        });
    });

    describe('byteToChar32', function () {
        it('returns non-guid strings unchanged', function () {
            $result = $this->arr->byteToChar32('normal text');

            expect($result)->toBe('normal text');
        });

        it('handles arrays with normal strings', function () {
            $input  = ['text1', 'text2'];
            $result = $this->arr->byteToChar32($input);

            expect($result)->toBe(['text1', 'text2']);
        });

        it('handles nested arrays', function () {
            $input  = [
                'field'  => 'value',
                'nested' => ['field' => 'value'],
            ];

            $result = $this->arr->byteToChar32($input);

            expect($result['field'])->toBe('value')
                ->and($result['nested']['field'])->toBe('value');
        });
    });
});
