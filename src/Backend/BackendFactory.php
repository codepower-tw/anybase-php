<?php

declare(strict_types=1);

namespace Anybase\Backend;

use Anybase\Exception\MissingExtensionException;

final class BackendFactory
{
    public static function auto(?string $sample): MathBackend
    {
        if ($sample === null || self::fitsInNativeInt($sample)) {
            return new NativeBackend();
        }

        if (extension_loaded('gmp')) {
            return new GmpBackend();
        }

        if (extension_loaded('bcmath')) {
            return new BcmathBackend();
        }

        throw new MissingExtensionException(
            "Value '{$sample}' exceeds PHP_INT_MAX and neither ext-gmp nor ext-bcmath is loaded."
        );
    }

    private static function fitsInNativeInt(string $sample): bool
    {
        if ($sample === '' || !ctype_digit($sample)) {
            return false;
        }
        $intVal = (int) $sample;
        return (string) $intVal === $sample;
    }

    private function __construct() {}
}
