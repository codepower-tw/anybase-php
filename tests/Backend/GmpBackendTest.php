<?php

declare(strict_types=1);

use Anybase\Backend\GmpBackend;
use Anybase\Exception\InvalidInputException;

beforeEach(function () {
    if (! extension_loaded('gmp')) {
        $this->markTestSkipped('ext-gmp not loaded');
    }
    $this->b = new GmpBackend();
});

it('reports zero correctly', function () {
    expect($this->b->isZero('0'))->toBeTrue()
        ->and($this->b->isZero('1'))->toBeFalse();
});

it('computes divmod on huge numbers', function () {
    $huge = '340282366920938463463374607431768211456'; // 2^128
    [$q, $r] = $this->b->divmod($huge, 10);

    expect($q)->toBe('34028236692093846346337460743176821145')
        ->and($r)->toBe(6);
});

it('computes mulAdd on huge numbers', function () {
    expect($this->b->mulAdd('12345678901234567890123456789', 10, 1))
        ->toBe('123456789012345678901234567891');
});

it('computes XOR on huge numbers (matches gmp_xor reference)', function () {
    $a = '340282366920938463463374607431768211455'; // 2^128 - 1
    $b = '170141183460469231731687303715884105727'; // 2^127 - 1
    $expected = (string) gmp_xor($a, $b);

    expect($this->b->xor($a, $b))->toBe($expected);
});

it('reports its name', function () {
    expect($this->b->name())->toBe('gmp');
});

it('rejects non-numeric strings', function () {
    $this->b->divmod('xyz', 10);
})->throws(InvalidInputException::class);

it('rejects negative numbers', function () {
    $this->b->divmod('-1', 10);
})->throws(InvalidInputException::class);
