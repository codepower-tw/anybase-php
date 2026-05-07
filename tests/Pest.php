<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Pest configuration
|--------------------------------------------------------------------------
|
| This file is loaded by Pest before any tests run. We deliberately keep
| it small: no global `uses()` so each test file is plain PHP, and only
| custom expectations / helpers that pay rent across multiple test files.
|
*/

// PHP 8.5 surfaces multiple deprecations from Pest 2 / PHPUnit 10 internals
// (ReflectionMethod::setAccessible, ReflectionProperty::setAccessible,
// implicit nullable parameters, etc). They're not our code, but they pollute
// every test with a DEPR marker. Swallow E_DEPRECATED triggers whose origin
// is inside vendor/; let everything from our own paths propagate.
$vendorPath = realpath(__DIR__ . '/../vendor') ?: __DIR__ . '/../vendor';
$previousHandler = set_error_handler(static function (
    int $severity,
    string $message,
    string $file,
    int $line,
) use (&$previousHandler, $vendorPath): bool {
    if ($severity === E_DEPRECATED && str_starts_with($file, $vendorPath)) {
        return true;
    }
    return $previousHandler === null
        ? false
        : (bool) $previousHandler($severity, $message, $file, $line);
});
