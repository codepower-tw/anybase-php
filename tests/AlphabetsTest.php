<?php

declare(strict_types=1);

use Anybase\Alphabets;

dataset('predefinedAlphabets', [
    'base2'              => ['base2',            2,  '01'],
    'base8'              => ['base8',            8,  '01234567'],
    'base10'             => ['base10',           10, '0123456789'],
    'base16'             => ['base16',           16, '0123456789abcdef'],
    'base16Upper'        => ['base16Upper',      16, '0123456789ABCDEF'],
    'base32Rfc4648'      => ['base32Rfc4648',    32, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'],
    'base32Hex'          => ['base32Hex',        32, '0123456789ABCDEFGHIJKLMNOPQRSTUV'],
    'crockfordBase32'    => ['crockfordBase32',  32, '0123456789ABCDEFGHJKMNPQRSTVWXYZ'],
    'base36'             => ['base36',           36, '0123456789abcdefghijklmnopqrstuvwxyz'],
    'base58Bitcoin'      => ['base58Bitcoin',    58, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz'],
    'base58Flickr'       => ['base58Flickr',     58, '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'],
    'base62'             => ['base62',           62, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'],
    'base64Url'          => ['base64Url',        64, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_'],
    'supercellHashtag'   => ['supercellHashtag', 14, '0289PYLQGRJCUV'],
]);

it('exposes the expected base and characters', function (string $method, int $base, string $chars) {
    $a = Alphabets::$method();

    expect($a->base())->toBe($base)
        ->and(implode('', $a->chars()))->toBe($chars);
})->with('predefinedAlphabets');

it('folds I, L, and O for Crockford and is case-insensitive', function () {
    $a = Alphabets::crockfordBase32();

    expect($a->indexOf('I'))->toBe(1)
        ->and($a->indexOf('i'))->toBe(1)
        ->and($a->indexOf('L'))->toBe(1)
        ->and($a->indexOf('l'))->toBe(1)
        ->and($a->indexOf('O'))->toBe(0)
        ->and($a->indexOf('o'))->toBe(0)
        ->and($a->indexOf('a'))->toBe(10);
});
