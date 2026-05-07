<?php

declare(strict_types=1);

namespace Anybase\Backend;

use Anybase\Exception\InvalidInputException;
use Anybase\Exception\OverflowException;

final class NativeBackend implements MathBackend
{
    public function isZero(string $n): bool
    {
        return $this->toInt($n) === 0;
    }

    public function divmod(string $n, int $base): array
    {
        $i = $this->toInt($n);
        return [(string) intdiv($i, $base), $i % $base];
    }

    public function mulAdd(string $acc, int $base, int $digit): string
    {
        $a = $this->toInt($acc);
        if ($a > intdiv(PHP_INT_MAX - $digit, $base)) {
            throw new OverflowException(
                "NativeBackend overflow: ({$acc} * {$base}) + {$digit} exceeds PHP_INT_MAX. "
                . 'Use GmpBackend or BcmathBackend for big integers.'
            );
        }
        return (string) ($a * $base + $digit);
    }

    public function xor(string $a, string $b): string
    {
        return (string) ($this->toInt($a) ^ $this->toInt($b));
    }

    public function name(): string
    {
        return 'native';
    }

    private function toInt(string $n): int
    {
        if ($n === '' || !ctype_digit($n)) {
            throw new InvalidInputException("Expected non-negative integer string, got: '{$n}'");
        }
        $intVal = (int) $n;
        if ((string) $intVal !== $n) {
            throw new OverflowException("Value '{$n}' exceeds PHP_INT_MAX for NativeBackend.");
        }
        return $intVal;
    }
}
