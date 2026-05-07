<?php

declare(strict_types=1);

use Anybase\Alphabets;
use Anybase\Codec;
use Anybase\Exception\InvalidInputException;

it('left-pads short values with the alphabet zero char', function () {
    $codec = Codec::for(Alphabets::base16())->withMinLength(4);

    expect($codec->encode(1))->toBe('0001')
        ->and($codec->encode(255))->toBe('00ff');
});

it('does not truncate long values', function () {
    $codec = Codec::for(Alphabets::base16())->withMinLength(2);

    expect($codec->encode(256))->toBe('100');
});

it('decodes padded strings transparently', function () {
    $codec = Codec::for(Alphabets::base62())->withMinLength(8);
    $padded = $codec->encode(42);

    expect(strlen($padded))->toBe(8)
        ->and($codec->decode($padded))->toBe('42')
        ->and($codec->decode(ltrim($padded, '0')))->toBe('42');
});

it('encodes zero as padded zero', function () {
    $codec = Codec::for(Alphabets::base16())->withMinLength(3);

    expect($codec->encode(0))->toBe('000');
});

it('rejects negative minLength', function () {
    Codec::for(Alphabets::base16())->withMinLength(-1);
})->throws(InvalidInputException::class);
