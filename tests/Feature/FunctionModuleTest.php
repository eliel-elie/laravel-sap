<?php

describe('FunctionModule', function () {
    describe('parameter validation', function () {
        it('throws exception for non-existent parameter', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('throws exception for type mismatch - table expects array', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('throws exception for type mismatch - char expects string', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('throws exception for type mismatch - int expects integer', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('accepts valid parameters', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });
    });

    describe('param method', function () {
        it('returns self for method chaining', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('stores parameter value', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('allows multiple parameters', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });
    });

    describe('execute', function () {
        it('invokes function and returns result', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('passes parameters to RFC function', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('applies rtrim option', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('throws FunctionCallException on error', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });
    });

    describe('description', function () {
        it('returns function description', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('returns collection of parameters', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });

        it('excludes name from description', function () {
            $this->markTestSkipped('Requires SAP connection mock');
        });
    });
});
