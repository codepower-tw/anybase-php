<?php

declare(strict_types=1);

use Anybase\Alphabets;
use Anybase\Codec;

dataset('vectors', [
    'base16 zero'        => ['base16',           0,         '0'],
    'base16 255'         => ['base16',           255,       'ff'],
    'base16 4096'        => ['base16',           4096,      '1000'],
    'base62 0'           => ['base62',           0,         '0'],
    'base62 61'          => ['base62',           61,        'z'],
    'base62 62'          => ['base62',           62,        '10'],
    'base62 123456789'   => ['base62',           123456789, '8M0kX'],
    'crockford 0'        => ['crockfordBase32',  0,         '0'],
    'crockford 31'       => ['crockfordBase32',  31,        'Z'],
    'crockford 32'       => ['crockfordBase32',  32,        '10'],
    'crockford 1234567'  => ['crockfordBase32',  1234567,   '15NM7'],
    'supercell 0'        => ['supercellHashtag', 0,         '0'],
    'supercell 13'       => ['supercellHashtag', 13,        'V'],
    'supercell 14'       => ['supercellHashtag', 14,        '20'],
    'base58Bitcoin 0'    => ['base58Bitcoin',    0,         '1'],
    'base58Bitcoin 57'   => ['base58Bitcoin',    57,        'z'],
    'base58Bitcoin 58'   => ['base58Bitcoin',    58,        '21'],
]);

it('encodes and decodes known vectors', function (string $alphabetMethod, int|string $input, string $expected) {
    $codec = Codec::for(Alphabets::$alphabetMethod());

    expect($codec->encode($input))->toBe($expected)
        ->and($codec->decode($expected))->toBe((string) $input);
})->with('vectors');
