<?php

declare(strict_types=1);

namespace Anybase\Backend;

interface MathBackend
{
    public function isZero(string $n): bool;

    /** @return array{0: string, 1: int}  [quotient, remainder] */
    public function divmod(string $n, int $base): array;

    /** Compute (acc * base) + digit. */
    public function mulAdd(string $acc, int $base, int $digit): string;

    /** Bitwise XOR of two non-negative integers, both passed as numeric strings. */
    public function xor(string $a, string $b): string;

    public function name(): string;
}
