<?php

use Elielelie\Sap\Functions\QueryBuilder;

describe('QueryBuilder', function () {
    beforeEach(function () {
        $this->builder = new QueryBuilder;
    });

    describe('where', function () {
        it('adds simple where condition', function () {
            $this->builder->where('BNAME', '=', 'USER');

            $options = $this->builder->options();

            expect($options)->toHaveCount(1)
                ->and($options[0]['TEXT'])->toBe("BNAME = 'USER'");
        });

        it('assumes equals operator when not provided', function () {
            $this->builder->where('BNAME', 'USER');

            $options = $this->builder->options();

            expect($options[0]['TEXT'])->toBe("BNAME = 'USER'");
        });

        it('converts column to uppercase', function () {
            $this->builder->where('bname', 'USER');

            $options = $this->builder->options();

            expect($options[0]['TEXT'])->toContain('BNAME');
        });

        it('handles different operators', function () {
            $this->builder->where('AGE', '>', '18');

            $options = $this->builder->options();

            expect($options[0]['TEXT'])->toBe("AGE > '18'");
        });

        it('chains multiple where conditions with AND', function () {
            $this->builder
                ->where('BNAME', 'USER')
                ->where('STATUS', 'ACTIVE');

            $options = $this->builder->options();

            expect($options)->toHaveCount(3)
                ->and($options[0]['TEXT'])->toBe("BNAME = 'USER'")
                ->and($options[1]['TEXT'])->toBe('AND')
                ->and($options[2]['TEXT'])->toBe("STATUS = 'ACTIVE'");
        });
    });

    describe('orWhere', function () {
        it('adds OR condition', function () {
            $this->builder
                ->where('BNAME', 'USER1')
                ->orWhere('BNAME', 'USER2');

            $options = $this->builder->options();

            expect($options)->toHaveCount(3)
                ->and($options[1]['TEXT'])->toBe('OR');
        });

        it('accepts operator parameter', function () {
            $this->builder->orWhere('AGE', '<', '30');

            $options = $this->builder->options();

            expect($options[0]['TEXT'])->toBe("AGE < '30'");
        });
    });

    describe('andWhere', function () {
        it('adds AND condition explicitly', function () {
            $this->builder
                ->where('BNAME', 'USER')
                ->andWhere('STATUS', 'ACTIVE');

            $options = $this->builder->options();

            expect($options[1]['TEXT'])->toBe('AND');
        });
    });

    describe('where with array (IN clause)', function () {
        it('creates OR conditions for array values', function () {
            $this->builder->where('BNAME', '=', ['USER1', 'USER2', 'USER3']);

            $options = $this->builder->options();

            expect($options)->toContain(['TEXT' => '('])
                ->and($options)->toContain(['TEXT' => ')'])
                ->and($options)->toContain(['TEXT' => 'OR']);
        });

        it('handles array with LIKE operator', function () {
            $this->builder->where('STATUS', 'LIKE', ['ACT*', 'PEN*']);

            $options = $this->builder->options();

            $text    = collect($options)->pluck('TEXT')->implode(' ');

            expect($text)->toContain('LIKE')
                ->and($text)->toContain('ACT*')
                ->and($text)->toContain('PEN*');
        });
    });

    describe('where with Closure (grouped conditions)', function () {
        it('creates grouped conditions with parentheses', function () {
            $this->builder->where(function ($query) {
                $query->where('BNAME', 'USER1')
                    ->orWhere('BNAME', 'USER2');
            });

            $options = $this->builder->options();

            expect($options[0]['TEXT'])->toBe('(')
                ->and($options[count($options) - 1]['TEXT'])->toBe(')');
        });

        it('combines groups with AND', function () {
            $this->builder
                ->where(function ($query) {
                    $query->where('BNAME', 'USER1');
                })
                ->where(function ($query) {
                    $query->where('STATUS', 'ACTIVE');
                });

            $options  = $this->builder->options();

            $andCount = collect($options)->filter(fn ($item) => $item['TEXT'] === 'AND')->count();

            expect($andCount)->toBe(1);
        });

        it('allows nested groups', function () {
            $this->builder->where(function ($query) {
                $query->where('BNAME', 'USER1')
                    ->where(function ($subQuery) {
                        $subQuery->where('STATUS', 'ACTIVE')
                            ->where('ROLE', 'ADMIN');
                    });
            });

            $options     = $this->builder->options();

            $openParens  = collect($options)->filter(fn ($item) => $item['TEXT'] === '(')->count();
            $closeParens = collect($options)->filter(fn ($item) => $item['TEXT'] === ')')->count();

            expect($openParens)->toBe(2)
                ->and($closeParens)->toBe(2);
        });
    });

    describe('complex queries', function () {
        it('builds complex query with multiple conditions', function () {
            $this->builder
                ->where('MANDT', '100')
                ->where(function ($query) {
                    $query->where('BNAME', '=', ['USER1', 'USER2'])
                        ->where('ROLE', 'ADMIN');
                })
                ->where('STATUS', '!=', 'LOCKED');

            $options = $this->builder->options();

            expect($options)->toBeArray()
                ->and(count($options))->toBeGreaterThan(5);
        });

        it('generates correct OPTIONS structure', function () {
            $this->builder
                ->where('FIELD1', 'VALUE1')
                ->orWhere('FIELD2', 'VALUE2');

            $options = $this->builder->options();

            expect($options)->each(fn ($item) => $item->toHaveKey('TEXT'));
        });
    });

    describe('options method', function () {
        it('returns empty array when no conditions', function () {
            $options = $this->builder->options();

            expect($options)->toBe([]);
        });

        it('returns array of TEXT entries', function () {
            $this->builder->where('BNAME', 'USER');

            $options = $this->builder->options();

            expect($options[0])->toHaveKey('TEXT');
        });
    });
});
