<?php

declare(strict_types=1);

use Anybase\Backend\BackendFactory;
use Anybase\Backend\NativeBackend;
use Anybase\Exception\MissingExtensionException;

it('picks NativeBackend for values that fit PHP_INT_MAX', function () {
    expect(BackendFactory::auto('12345'))->toBeInstanceOf(NativeBackend::class);
});

it('picks NativeBackend when no sample is given', function () {
    expect(BackendFactory::auto(null))->toBeInstanceOf(NativeBackend::class);
});

it('picks GmpBackend for large values when gmp is available', function () {
    if (! extension_loaded('gmp')) {
        $this->markTestSkipped('ext-gmp not loaded');
    }
    expect(BackendFactory::auto('99999999999999999999')->name())->toBe('gmp');
});

it('throws MissingExtensionException when no big-int backend is available and value is oversized', function () {
    if (extension_loaded('gmp') || extension_loaded('bcmath')) {
        $this->markTestSkipped('a big-int backend is available');
    }
    BackendFactory::auto('99999999999999999999');
})->throws(MissingExtensionException::class);
