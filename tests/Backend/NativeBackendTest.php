<?php

declare(strict_types=1);

use Anybase\Backend\NativeBackend;
use Anybase\Exception\InvalidInputException;
use Anybase\Exception\OverflowException;

beforeEach(function () {
    $this->b = new NativeBackend();
});

it('reports zero correctly', function () {
    expect($this->b->isZero('0'))->toBeTrue()
        ->and($this->b->isZero('1'))->toBeFalse()
        ->and($this->b->isZero('123'))->toBeFalse();
});

it('computes divmod', function () {
    expect($this->b->divmod('124', 3))->toBe(['41', 1])
        ->and($this->b->divmod('7', 10))->toBe(['0', 7])
        ->and($this->b->divmod('0', 16))->toBe(['0', 0]);
});

it('computes mulAdd', function () {
    expect($this->b->mulAdd('12', 10, 3))->toBe('123')
        ->and($this->b->mulAdd('0', 16, 0))->toBe('0');
});

it('computes XOR matching native PHP int xor', function () {
    expect($this->b->xor('123', '456'))->toBe((string) (123 ^ 456))
        ->and($this->b->xor('5', '5'))->toBe('0');
});

it('reports its name', function () {
    expect($this->b->name())->toBe('native');
});

it('rejects negative numbers', function () {
    $this->b->divmod('-1', 10);
})->throws(InvalidInputException::class);

it('rejects non-numeric strings', function () {
    $this->b->divmod('abc', 10);
})->throws(InvalidInputException::class);

it('throws OverflowException when mulAdd would exceed PHP_INT_MAX', function () {
    $this->b->mulAdd((string) PHP_INT_MAX, 10, 0);
})->throws(OverflowException::class);

it('throws OverflowException when input exceeds PHP_INT_MAX', function () {
    $this->b->divmod('99999999999999999999', 10);
})->throws(OverflowException::class);
