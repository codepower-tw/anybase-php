# anybase-php

Convert non-negative integers (including DB-primary-key big integers) between
arbitrary numeric bases using custom alphabets. For URL-friendly ID
obfuscation, short codes, and human-readable hashing — **not** for
cryptographic security.

## Install

    composer require codepower/anybase

PHP 8.1+. Optional: `ext-gmp` (preferred) or `ext-bcmath` for integers larger
than `PHP_INT_MAX`.

## Quick start

```php
use Anybase\Codec;
use Anybase\Alphabets;

$codec = Codec::for(Alphabets::base62());
$codec->encode(123456789);   // "8M0kX"
$codec->decode("8M0kX");     // "123456789"
```

## Custom alphabet

```php
use Anybase\Alphabet;

$codec = Codec::for(Alphabet::fromString('0289PYLQGRJCUV'));
$codec->encode(98765); // "8QVUR"
```

## Salt (per-environment scrambling)

XOR the integer with a fixed salt so that staging IDs and prod IDs don't
visually overlap. **This is not encryption** — anyone with one plaintext-
ciphertext pair can recover the salt.

```php
$codec = Codec::for(Alphabets::crockfordBase32())->withSalt(0xDEADBEEF);
```

## Padding

```php
$codec = Codec::for(Alphabets::base62())->withMinLength(8);
$codec->encode(42);   // "0000000g"
```

## Convert between bases

```php
use Anybase\BaseConverter;

$conv = new BaseConverter(Alphabets::base16(), Alphabets::base62());
$conv->convert('deadbeef');
```

## Predefined alphabets

`base2` `base8` `base10` `base16` `base16Upper` `base32Rfc4648` `base32Hex`
`crockfordBase32` `base36` `base58Bitcoin` `base58Flickr` `base62`
`base64Url` `supercellHashtag`

Crockford base32 folds `I/L → 1` and `O → 0` on decode and is case-insensitive.

Supercell hashtag folds `I/1 → L`, `O → 0`, `B → 8` on decode and is
case-insensitive by default. Both behaviors are configurable:

```php
Alphabets::supercellHashtag();                 // default foldings + case-insensitive
Alphabets::supercellHashtag([]);               // no foldings, still case-insensitive
Alphabets::supercellHashtag(['Z' => '2']);     // custom fold map
Alphabets::supercellHashtag([], caseInsensitive: false); // strict
```

## Big integers

For values larger than `PHP_INT_MAX` (2^63 − 1), install `ext-gmp` (preferred)
or `ext-bcmath`. The library auto-selects:

1. Native PHP int when the value fits.
2. GMP when available.
3. BCMath as fallback.

Pin a specific backend with `->withBackend(new \Anybase\Backend\NativeBackend())`.

## Errors

All exceptions implement `\Anybase\Exception\AnybaseException`. Specific
classes: `InvalidAlphabetException`, `InvalidInputException`,
`OverflowException`, `MissingExtensionException`.

## Testing

This package uses Pest 2:

    composer test

## License

MIT.
