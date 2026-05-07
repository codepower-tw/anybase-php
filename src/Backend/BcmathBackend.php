<?php

declare(strict_types=1);

namespace Anybase\Backend;

use Anybase\Exception\InvalidInputException;
use Anybase\Exception\MissingExtensionException;

final class BcmathBackend implements MathBackend
{
    /** Limb size: 2^30. Keeps (limb * 2^30) safely under PHP_INT_MAX (2^63-1) on 64-bit PHP. */
    private const LIMB_BASE = '1073741824';

    public function __construct()
    {
        if (!extension_loaded('bcmath')) {
            throw new MissingExtensionException('BcmathBackend requires the bcmath extension.');
        }
    }

    public function isZero(string $n): bool
    {
        return bccomp($this->parse($n), '0', 0) === 0;
    }

    public function divmod(string $n, int $base): array
    {
        $n = $this->parse($n);
        $bs = (string) $base;
        $q = bcdiv($n, $bs, 0);
        $r = bcmod($n, $bs);
        return [$q, (int) $r];
    }

    public function mulAdd(string $acc, int $base, int $digit): string
    {
        return bcadd(bcmul($this->parse($acc), (string) $base, 0), (string) $digit, 0);
    }

    public function xor(string $a, string $b): string
    {
        $aLimbs = $this->toLimbs($this->parse($a));
        $bLimbs = $this->toLimbs($this->parse($b));
        $len = max(count($aLimbs), count($bLimbs));
        $out = [];
        for ($i = 0; $i < $len; $i++) {
            $out[] = ($aLimbs[$i] ?? 0) ^ ($bLimbs[$i] ?? 0);
        }
        return $this->fromLimbs($out);
    }

    public function name(): string
    {
        return 'bcmath';
    }

    /** @return list<int> least-significant limb first */
    private function toLimbs(string $n): array
    {
        $limbs = [];
        while (bccomp($n, '0', 0) > 0) {
            $limbs[] = (int) bcmod($n, self::LIMB_BASE);
            $n = bcdiv($n, self::LIMB_BASE, 0);
        }
        return $limbs;
    }

    /** @param list<int> $limbs least-significant limb first */
    private function fromLimbs(array $limbs): string
    {
        $n = '0';
        for ($i = count($limbs) - 1; $i >= 0; $i--) {
            $n = bcadd(bcmul($n, self::LIMB_BASE, 0), (string) $limbs[$i], 0);
        }
        return $n;
    }

    private function parse(string $n): string
    {
        if ($n === '' || !ctype_digit($n)) {
            throw new InvalidInputException("Expected non-negative integer string, got: '{$n}'");
        }
        return $n;
    }
}
