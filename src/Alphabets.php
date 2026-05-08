<?php

declare(strict_types=1);

namespace Anybase;

final class Alphabets
{
    public static function base2(): Alphabet
    {
        return Alphabet::fromString('01');
    }
    public static function base8(): Alphabet
    {
        return Alphabet::fromString('01234567');
    }
    public static function base10(): Alphabet
    {
        return Alphabet::fromString('0123456789');
    }
    public static function base16(): Alphabet
    {
        return Alphabet::fromString('0123456789abcdef');
    }
    public static function base16Upper(): Alphabet
    {
        return Alphabet::fromString('0123456789ABCDEF');
    }
    public static function base32Rfc4648(): Alphabet
    {
        return Alphabet::fromString('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567');
    }
    public static function base32Hex(): Alphabet
    {
        return Alphabet::fromString('0123456789ABCDEFGHIJKLMNOPQRSTUV');
    }

    public static function crockfordBase32(): Alphabet
    {
        return Alphabet::fromString('0123456789ABCDEFGHJKMNPQRSTVWXYZ')
            ->withFolding(['I' => '1', 'L' => '1', 'O' => '0'], caseInsensitive: true);
    }

    public static function base36(): Alphabet
    {
        return Alphabet::fromString('0123456789abcdefghijklmnopqrstuvwxyz');
    }
    public static function base58Bitcoin(): Alphabet
    {
        return Alphabet::fromString('123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
    }
    public static function base58Flickr(): Alphabet
    {
        return Alphabet::fromString('123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ');
    }
    public static function base62(): Alphabet
    {
        return Alphabet::fromString('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
    }
    public static function base64Url(): Alphabet
    {
        return Alphabet::fromString('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_');
    }
    public static function supercellHashtag(): Alphabet
    {
        // Supercell deliberately omits visually ambiguous characters from
        // the alphabet. On decode we forgive common confusions: I/1 -> L,
        // O -> 0, B -> 8. Case-insensitive so users can type either case.
        return Alphabet::fromString('0289PYLQGRJCUV')
            ->withFolding(
                ['I' => 'L', '1' => 'L', 'O' => '0', 'B' => '8'],
                caseInsensitive: true,
            );
    }

    private function __construct()
    {
    }
}
