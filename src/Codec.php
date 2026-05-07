<?php

declare(strict_types=1);

namespace Anybase;

use Anybase\Backend\BackendFactory;
use Anybase\Backend\MathBackend;
use Anybase\Backend\NativeBackend;
use Anybase\Exception\InvalidAlphabetException;
use Anybase\Exception\InvalidInputException;
use Anybase\Exception\OverflowException;

final class Codec
{
    private function __construct(
        private readonly Alphabet $alphabet,
        private readonly ?string $salt = null,
        private readonly int $minLength = 0,
        private readonly ?MathBackend $backend = null,
    ) {
    }

    public static function for(Alphabet $alphabet): self
    {
        return new self($alphabet);
    }

    public function withSalt(int|string $salt): self
    {
        return new self($this->alphabet, $this->normalize($salt), $this->minLength, $this->backend);
    }

    public function withMinLength(int $n): self
    {
        if ($n < 0) {
            throw new InvalidInputException("minLength must be >= 0, got {$n}");
        }
        return new self($this->alphabet, $this->salt, $n, $this->backend);
    }

    public function withBackend(MathBackend $backend): self
    {
        return new self($this->alphabet, $this->salt, $this->minLength, $backend);
    }

    public function encode(int|string $number): string
    {
        $n = $this->normalize($number);
        $backend = $this->backend ?? BackendFactory::auto($n);

        if ($this->salt !== null) {
            $n = $backend->xor($n, $this->salt);
        }

        $base = $this->alphabet->base();
        if ($backend->isZero($n)) {
            return $this->pad($this->alphabet->zero());
        }

        $out = '';
        while (!$backend->isZero($n)) {
            [$n, $r] = $backend->divmod($n, $base);
            $out = $this->alphabet->charAt($r) . $out;
        }
        return $this->pad($out);
    }

    public function decode(string $encoded): string
    {
        if ($encoded === '') {
            throw new InvalidInputException('Cannot decode empty string.');
        }

        $base = $this->alphabet->base();
        // Start with native backend; will auto-promote if overflow is detected.
        $backend = $this->backend ?? new NativeBackend();
        $n = '0';

        foreach (mb_str_split($encoded) as $c) {
            try {
                $idx = $this->alphabet->indexOf($c);
            } catch (InvalidAlphabetException $e) {
                throw new InvalidInputException(
                    "Character '{$c}' is not valid for this alphabet.",
                    previous: $e,
                );
            }
            try {
                $n = $backend->mulAdd($n, $base, $idx);
            } catch (OverflowException $e) {
                if ($this->backend !== null) {
                    // User pinned a backend; don't auto-promote, propagate.
                    throw $e;
                }
                // Auto-promote to a big-integer backend and retry this step.
                $backend = BackendFactory::auto('99999999999999999999');
                $n = $backend->mulAdd($n, $base, $idx);
            }
        }

        if ($this->salt !== null) {
            $n = $backend->xor($n, $this->salt);
        }

        return $n;
    }

    private function pad(string $s): string
    {
        $len = mb_strlen($s);
        if ($len >= $this->minLength) {
            return $s;
        }
        return str_repeat($this->alphabet->zero(), $this->minLength - $len) . $s;
    }

    private function normalize(int|string $n): string
    {
        if (is_int($n)) {
            if ($n < 0) {
                throw new InvalidInputException("Negative integers are not supported: {$n}");
            }
            return (string) $n;
        }
        if ($n === '' || !ctype_digit($n)) {
            throw new InvalidInputException("Expected non-negative integer string, got: '{$n}'");
        }
        return $n;
    }
}
