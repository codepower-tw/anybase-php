<?php

declare(strict_types=1);

use Anybase\Alphabets;
use Anybase\BaseConverter;

it('converts hex to base62 and back', function () {
    $hexToB10 = new BaseConverter(Alphabets::base16(), Alphabets::base10());
    expect($hexToB10->convert('deadbeef'))->toBe('3735928559');

    $hexToB62 = new BaseConverter(Alphabets::base16(), Alphabets::base62());
    $b62 = $hexToB62->convert('deadbeef');

    $b62ToHex = new BaseConverter(Alphabets::base62(), Alphabets::base16());
    expect($b62ToHex->convert($b62))->toBe('deadbeef');
});

it('converts binary to hex', function () {
    $conv = new BaseConverter(Alphabets::base2(), Alphabets::base16());

    expect($conv->convert('11111111'))->toBe('ff')
        ->and($conv->convert('100000000'))->toBe('100');
});

it('is identity when source and target alphabets are the same', function () {
    $conv = new BaseConverter(Alphabets::base62(), Alphabets::base62());

    expect($conv->convert('hello'))->toBe('hello');
});

it('handles big numbers when gmp or bcmath is available', function () {
    if (! extension_loaded('gmp') && ! extension_loaded('bcmath')) {
        $this->markTestSkipped('requires gmp or bcmath');
    }
    $hex = 'ffffffffffffffffffffffffffffffff'; // 2^128 - 1
    $conv = new BaseConverter(Alphabets::base16(), Alphabets::base10());

    expect($conv->convert($hex))->toBe('340282366920938463463374607431768211455');
});
