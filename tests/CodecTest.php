<?php

declare(strict_types=1);

use Anybase\Alphabets;
use Anybase\Codec;
use Anybase\Exception\InvalidInputException;

it('encodes zero', function () {
    expect(Codec::for(Alphabets::base16())->encode(0))->toBe('0');
});

it('roundtrips encode/decode in base 10 for 0..100', function () {
    $codec = Codec::for(Alphabets::base10());
    for ($i = 0; $i <= 100; $i++) {
        expect($codec->encode($i))->toBe((string) $i)
            ->and($codec->decode((string) $i))->toBe((string) $i);
    }
});

it('encodes and decodes base16', function () {
    $codec = Codec::for(Alphabets::base16());
    expect($codec->encode(255))->toBe('ff')
        ->and($codec->encode(256))->toBe('100')
        ->and($codec->decode('ff'))->toBe('255')
        ->and($codec->decode('100'))->toBe('256');
});

it('encodes 123456789 in base62 to 8M0kX', function () {
    // 123456789 / 62 = 1991238 r 33  ('X')
    // 1991238   / 62 = 32116   r 46  ('k')
    // 32116     / 62 = 518     r 0   ('0')
    // 518       / 62 = 8       r 22  ('M')
    // 8         / 62 = 0       r 8   ('8')
    $codec = Codec::for(Alphabets::base62());

    expect($codec->encode(123456789))->toBe('8M0kX')
        ->and($codec->decode('8M0kX'))->toBe('123456789');
});

it('roundtrips a big integer', function () {
    if (! extension_loaded('gmp') && ! extension_loaded('bcmath')) {
        $this->markTestSkipped('requires gmp or bcmath');
    }
    $codec = Codec::for(Alphabets::base62());
    $big = '99999999999999999999';

    expect($codec->decode($codec->encode($big)))->toBe($big);
});

it('rejects characters not in the alphabet on decode', function () {
    Codec::for(Alphabets::base16())->decode('z');
})->throws(InvalidInputException::class);

it('rejects negative numbers on encode', function () {
    Codec::for(Alphabets::base10())->encode(-1);
})->throws(InvalidInputException::class);

it('rejects non-numeric strings on encode', function () {
    Codec::for(Alphabets::base10())->encode('abc');
})->throws(InvalidInputException::class);

it('decodes folded characters in Crockford', function () {
    $codec = Codec::for(Alphabets::crockfordBase32());

    expect($codec->decode('IL'))->toBe($codec->decode('11'))
        ->and($codec->decode('il'))->toBe($codec->decode('11'))
        ->and($codec->decode('OO'))->toBe($codec->decode('00'));
});

it('returns a new instance from each wither', function () {
    $a = Codec::for(Alphabets::base62());
    $b = $a->withMinLength(8);

    expect($b)->not->toBe($a);
});
