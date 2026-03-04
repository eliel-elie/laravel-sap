<?php

use Elielelie\Sap\Functions\QueryBuilder;

describe('QueryBuilder', function () {
    describe('where', function () {
        it('adds simple where condition', function () {
            $builder = new QueryBuilder;
            $builder->where('BNAME', '=', 'USER');

            $options = $builder->options();

            expect($options)->toHaveCount(1)
                ->and($options[0]['TEXT'])->toBe("BNAME = 'USER'");
        });

        it('assumes equals operator when not provided', function () {
            $builder = new QueryBuilder;
            $builder->where('BNAME', 'USER');

            $options = $builder->options();

            expect($options[0]['TEXT'])->toBe("BNAME = 'USER'");
        });

        it('converts column to uppercase', function () {
            $builder = new QueryBuilder;
            $builder->where('bname', 'USER');

            $options = $builder->options();

            expect($options[0]['TEXT'])->toContain('BNAME');
        });

        it('handles different operators', function () {
            $builder = new QueryBuilder;
            $builder->where('AGE', '>', '18');

            $options = $builder->options();

            expect($options[0]['TEXT'])->toBe("AGE > '18'");
        });

        it('chains multiple where conditions with AND', function () {
            $builder = new QueryBuilder;
            $builder
                ->where('BNAME', 'USER')
                ->where('STATUS', 'ACTIVE');

            $options = $builder->options();

            expect($options)->toHaveCount(3)
                ->and($options[0]['TEXT'])->toBe("BNAME = 'USER'")
                ->and($options[1]['TEXT'])->toBe('AND')
                ->and($options[2]['TEXT'])->toBe("STATUS = 'ACTIVE'");
        });
    });

    describe('orWhere', function () {
        it('adds OR condition', function () {
            $builder = new QueryBuilder;
            $builder
                ->where('BNAME', 'USER1')
                ->orWhere('BNAME', 'USER2');

            $options = $builder->options();

            expect($options)->toHaveCount(3)
                ->and($options[1]['TEXT'])->toBe('OR');
        });

        it('accepts operator parameter', function () {
            $builder = new QueryBuilder;
            $builder->orWhere('AGE', '<', '30');

            $options = $builder->options();

            expect($options[0]['TEXT'])->toBe("AGE < '30'");
        });
    });

    describe('andWhere', function () {
        it('adds AND condition explicitly', function () {
            $builder = new QueryBuilder;
            $builder
                ->where('BNAME', 'USER')
                ->andWhere('STATUS', 'ACTIVE');

            $options = $builder->options();

            expect($options[1]['TEXT'])->toBe('AND');
        });
    });

    describe('where with array (IN clause)', function () {
        it('creates OR conditions for array values', function () {
            $builder = new QueryBuilder;
            $builder->where('BNAME', '=', ['USER1', 'USER2', 'USER3']);

            $options = $builder->options();

            expect($options)->toContain(['TEXT' => '('])
                ->and($options)->toContain(['TEXT' => ')'])
                ->and($options)->toContain(['TEXT' => 'OR']);
        });

        it('handles array with LIKE operator', function () {
            $builder = new QueryBuilder;
            $builder->where('STATUS', 'LIKE', ['ACT*', 'PEN*']);

            $options = $builder->options();

            $text    = collect($options)->pluck('TEXT')->implode(' ');

            expect($text)->toContain('LIKE')
                ->and($text)->toContain('ACT*')
                ->and($text)->toContain('PEN*');
        });
    });

    describe('where with Closure (grouped conditions)', function () {
        it('creates grouped conditions with parentheses', function () {
            $builder = new QueryBuilder;
            $builder->where(function ($query) {
                $query->where('BNAME', 'USER1')
                    ->orWhere('BNAME', 'USER2');
            });

            $options = $builder->options();

            expect($options[0]['TEXT'])->toBe('(')
                ->and($options[count($options) - 1]['TEXT'])->toBe(')');
        });

        it('combines groups with AND', function () {
            $builder  = new QueryBuilder;
            $builder
                ->where(function ($query) {
                    $query->where('BNAME', 'USER1');
                })
                ->where(function ($query) {
                    $query->where('STATUS', 'ACTIVE');
                });

            $options  = $builder->options();

            $andCount = collect($options)->filter(fn ($item) => $item['TEXT'] === 'AND')->count();

            expect($andCount)->toBe(1);
        });

        it('allows nested groups', function () {
            $builder     = new QueryBuilder;
            $builder->where(function ($query) {
                $query->where('BNAME', 'USER1')
                    ->where(function ($subQuery) {
                        $subQuery->where('STATUS', 'ACTIVE')
                            ->where('ROLE', 'ADMIN');
                    });
            });

            $options     = $builder->options();

            $openParens  = collect($options)->filter(fn ($item) => $item['TEXT'] === '(')->count();
            $closeParens = collect($options)->filter(fn ($item) => $item['TEXT'] === ')')->count();

            expect($openParens)->toBe(2)
                ->and($closeParens)->toBe(2);
        });
    });

    describe('complex queries', function () {
        it('builds complex query with multiple conditions', function () {
            $builder = new QueryBuilder;
            $builder
                ->where('MANDT', '100')
                ->where(function ($query) {
                    $query->where('BNAME', '=', ['USER1', 'USER2'])
                        ->where('ROLE', 'ADMIN');
                })
                ->where('STATUS', '!=', 'LOCKED');

            $options = $builder->options();

            expect($options)->toBeArray()
                ->and(count($options))->toBeGreaterThan(5);
        });

        it('generates correct OPTIONS structure', function () {
            $builder = new QueryBuilder;
            $builder
                ->where('FIELD1', 'VALUE1')
                ->orWhere('FIELD2', 'VALUE2');

            $options = $builder->options();

            expect($options)->each(fn ($item) => $item->toHaveKey('TEXT'));
        });
    });

    describe('options method', function () {
        it('returns empty array when no conditions', function () {
            $builder = new QueryBuilder;
            $options = $builder->options();

            expect($options)->toBe([]);
        });

        it('returns array of TEXT entries', function () {
            $builder = new QueryBuilder;
            $builder->where('BNAME', 'USER');

            $options = $builder->options();

            expect($options[0])->toHaveKey('TEXT');
        });
    });
});
