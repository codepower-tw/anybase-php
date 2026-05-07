<?php

declare(strict_types=1);

use Anybase\Alphabets;
use Anybase\Codec;

it('roundtrips with a salt for many values', function () {
    $codec = Codec::for(Alphabets::base62())->withSalt(0xDEADBEEF);
    for ($i = 0; $i < 100; $i++) {
        expect($codec->decode($codec->encode($i)))->toBe((string) $i);
    }
});

it('produces different output for different salts', function () {
    $a = Codec::for(Alphabets::base62())->withSalt(0xCAFEBABE);
    $b = Codec::for(Alphabets::base62())->withSalt(0xDEADBEEF);

    expect($a->encode(12345))->not->toBe($b->encode(12345));
});

it('treats salt 0 as identity', function () {
    $plain  = Codec::for(Alphabets::base62());
    $salted = Codec::for(Alphabets::base62())->withSalt(0);

    expect($salted->encode(12345))->toBe($plain->encode(12345));
});

it('roundtrips a big integer with a big salt', function () {
    if (! extension_loaded('gmp') && ! extension_loaded('bcmath')) {
        $this->markTestSkipped('requires gmp or bcmath');
    }
    $codec = Codec::for(Alphabets::base62())->withSalt('99999999999999999999');
    $big = '12345678901234567890';

    expect($codec->decode($codec->encode($big)))->toBe($big);
});
