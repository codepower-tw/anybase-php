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
    /**
     * Supercell hashtag alphabet (e.g. Clash of Clans player tags).
     *
     * The alphabet 0289PYLQGRJCUV deliberately omits visually ambiguous
     * characters. By default we forgive common confusions on decode:
     * I/1 -> L, O -> 0, B -> 8, and accept either case.
     *
     * Pass `[]` to disable ambiguity folding (case-insensitivity is
     * controlled separately). Pass a custom map to override the defaults.
     *
     * @param array<int|string,string>|null $foldMap     Null = library defaults; [] = no folding; otherwise a custom alias->target map.
     * @param bool                      $caseInsensitive Default true. Set false for strict casing.
     */
    public static function supercellHashtag(?array $foldMap = null, bool $caseInsensitive = true): Alphabet
    {
        $foldMap ??= ['I' => 'L', '1' => 'L', 'O' => '0', 'B' => '8'];
        $a = Alphabet::fromString('0289PYLQGRJCUV');
        if ($foldMap === [] && !$caseInsensitive) {
            return $a;
        }
        return $a->withFolding($foldMap, caseInsensitive: $caseInsensitive);
    }

    private function __construct()
    {
    }
}
