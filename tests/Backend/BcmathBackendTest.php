<?php

declare(strict_types=1);

use Anybase\Backend\BcmathBackend;

beforeEach(function () {
    if (! extension_loaded('bcmath')) {
        $this->markTestSkipped('ext-bcmath not loaded');
    }
    $this->b = new BcmathBackend();
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

it('computes XOR matching native PHP int xor for small values', function () {
    expect($this->b->xor('123', '456'))->toBe((string) (123 ^ 456))
        ->and($this->b->xor((string) 0xDEADBEEF, (string) 0xCAFEBABE))->toBe((string) (0xDEADBEEF ^ 0xCAFEBABE))
        ->and($this->b->xor('0', '0'))->toBe('0')
        ->and($this->b->xor('5', '0'))->toBe('5');
});

it('XOR is its own inverse', function () {
    $a = '12345678901234567890';
    $salt = '987654321987654321';
    $scrambled = $this->b->xor($a, $salt);

    expect($this->b->xor($scrambled, $salt))->toBe($a);
});

it('XOR matches GMP on huge values', function () {
    if (! extension_loaded('gmp')) {
        $this->markTestSkipped('cross-check requires gmp');
    }
    $a = '340282366920938463463374607431768211455';
    $b = '170141183460469231731687303715884105727';
    $expected = gmp_strval(gmp_xor($a, $b));

    expect($this->b->xor($a, $b))->toBe($expected);
});

it('reports its name', function () {
    expect($this->b->name())->toBe('bcmath');
});
