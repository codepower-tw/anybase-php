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

it('folds I/1 to L, O to 0, and B to 8 for Supercell hashtag by default', function () {
    // Alphabet: 0289PYLQGRJCUV — indices: 0=0, 2=1, 8=2, 9=3, P=4, Y=5,
    // L=6, Q=7, G=8, R=9, J=10, C=11, U=12, V=13
    $a = Alphabets::supercellHashtag();

    expect($a->indexOf('I'))->toBe(6)   // I -> L
        ->and($a->indexOf('1'))->toBe(6)   // 1 -> L
        ->and($a->indexOf('i'))->toBe(6)
        ->and($a->indexOf('O'))->toBe(0)   // O -> 0
        ->and($a->indexOf('o'))->toBe(0)
        ->and($a->indexOf('B'))->toBe(2)   // B -> 8
        ->and($a->indexOf('b'))->toBe(2)
        ->and($a->indexOf('p'))->toBe(4)   // case-insensitive on alphabet members
        ->and($a->indexOf('v'))->toBe(13);
});

it('supercellHashtag accepts a custom fold map', function () {
    $a = Alphabets::supercellHashtag(['Z' => '2']);

    expect($a->indexOf('Z'))->toBe(1)   // Z -> 2 (custom)
        ->and($a->indexOf('z'))->toBe(1)
        ->and(fn () => $a->indexOf('I'))->toThrow(\Anybase\Exception\InvalidAlphabetException::class);
});

it('supercellHashtag with empty fold map disables ambiguity folding but keeps case-insensitivity', function () {
    $a = Alphabets::supercellHashtag([]);

    expect($a->indexOf('p'))->toBe(4)   // still case-insensitive
        ->and(fn () => $a->indexOf('I'))->toThrow(\Anybase\Exception\InvalidAlphabetException::class)
        ->and(fn () => $a->indexOf('B'))->toThrow(\Anybase\Exception\InvalidAlphabetException::class);
});

it('supercellHashtag in strict mode rejects lowercase and ambiguous characters', function () {
    $a = Alphabets::supercellHashtag([], caseInsensitive: false);

    expect($a->indexOf('P'))->toBe(4)
        ->and(fn () => $a->indexOf('p'))->toThrow(\Anybase\Exception\InvalidAlphabetException::class)
        ->and(fn () => $a->indexOf('I'))->toThrow(\Anybase\Exception\InvalidAlphabetException::class);
});
