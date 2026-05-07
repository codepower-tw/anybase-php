<?php

declare(strict_types=1);

namespace Anybase\Backend;

use Anybase\Exception\InvalidInputException;
use Anybase\Exception\MissingExtensionException;

final class GmpBackend implements MathBackend
{
    public function __construct()
    {
        if (!extension_loaded('gmp')) {
            throw new MissingExtensionException('GmpBackend requires the gmp extension.');
        }
    }

    public function isZero(string $n): bool
    {
        return gmp_cmp($this->parse($n), 0) === 0;
    }

    public function divmod(string $n, int $base): array
    {
        [$q, $r] = gmp_div_qr($this->parse($n), $base);
        return [gmp_strval($q), (int) gmp_strval($r)];
    }

    public function mulAdd(string $acc, int $base, int $digit): string
    {
        return gmp_strval(gmp_add(gmp_mul($this->parse($acc), $base), $digit));
    }

    public function xor(string $a, string $b): string
    {
        return gmp_strval(gmp_xor($this->parse($a), $this->parse($b)));
    }

    public function name(): string
    {
        return 'gmp';
    }

    private function parse(string $n): \GMP
    {
        if ($n === '' || !ctype_digit($n)) {
            throw new InvalidInputException("Expected non-negative integer string, got: '{$n}'");
        }
        return gmp_init($n, 10);
    }
}
