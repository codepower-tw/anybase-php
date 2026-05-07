<?php

declare(strict_types=1);

use Anybase\Alphabet;
use Anybase\Exception\InvalidAlphabetException;

it('stores chars and base when built from string', function () {
    $a = Alphabet::fromString('0123456789abcdef');

    expect($a->base())->toBe(16)
        ->and($a->zero())->toBe('0')
        ->and($a->charAt(10))->toBe('a')
        ->and($a->indexOf('a'))->toBe(10);
});

it('rejects an alphabet shorter than two characters', function () {
    Alphabet::fromString('x');
})->throws(InvalidAlphabetException::class);

it('rejects duplicate characters', function () {
    Alphabet::fromString('abca');
})->throws(InvalidAlphabetException::class);

it('throws when indexOf is given a character not in the alphabet', function () {
    Alphabet::fromString('01')->indexOf('2');
})->throws(InvalidAlphabetException::class);

it('supports multibyte characters', function () {
    $a = Alphabet::fromString('αβγδ');

    expect($a->base())->toBe(4)
        ->and($a->charAt(2))->toBe('γ')
        ->and($a->indexOf('γ'))->toBe(2);
});

it('treats two alphabets with the same chars in the same order as equal', function () {
    expect(Alphabet::fromString('01')->equals(Alphabet::fromString('01')))->toBeTrue()
        ->and(Alphabet::fromString('01')->equals(Alphabet::fromString('10')))->toBeFalse();
});

it('folds case when caseInsensitive is enabled', function () {
    $a = Alphabet::fromString('0123456789ABCDEF')->withFolding([], caseInsensitive: true);

    expect($a->indexOf('a'))->toBe(10)
        ->and($a->indexOf('A'))->toBe(10);
});

it('aliases characters via fold map (Crockford-style)', function () {
    $a = Alphabet::fromString('0123456789ABCDEFGHJKMNPQRSTVWXYZ')
        ->withFolding(['I' => '1', 'L' => '1', 'O' => '0'], caseInsensitive: true);

    expect($a->indexOf('I'))->toBe(1)
        ->and($a->indexOf('l'))->toBe(1)
        ->and($a->indexOf('O'))->toBe(0);
});

it('rejects fold maps that target characters outside the alphabet', function () {
    Alphabet::fromString('01')->withFolding(['I' => '9']);
})->throws(InvalidAlphabetException::class);
