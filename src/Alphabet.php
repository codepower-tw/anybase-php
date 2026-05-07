<?php

declare(strict_types=1);

namespace Anybase;

use Anybase\Exception\InvalidAlphabetException;

final class Alphabet
{
    /** @var list<string> */
    private readonly array $chars;
    /** @var array<string,int> */
    private readonly array $index;
    private readonly int $base;

    /**
     * @param list<string> $chars
     * @param array<string,string> $foldMap  e.g. ['I' => '1']; aliases get same index as target
     */
    private function __construct(
        array $chars,
        private readonly array $foldMap = [],
        private readonly bool $caseInsensitive = false,
    ) {
        $this->chars = $chars;
        $this->base = count($chars);

        if ($this->base < 2) {
            throw new InvalidAlphabetException('Alphabet must have at least 2 characters.');
        }

        $index = [];
        foreach ($chars as $i => $c) {
            $key = $caseInsensitive ? mb_strtolower($c) : $c;
            if (isset($index[$key])) {
                throw new InvalidAlphabetException("Duplicate character in alphabet: {$c}");
            }
            $index[$key] = $i;
        }

        foreach ($foldMap as $alias => $target) {
            $targetKey = $caseInsensitive ? mb_strtolower($target) : $target;
            if (!isset($index[$targetKey])) {
                throw new InvalidAlphabetException("Fold target '{$target}' is not in alphabet.");
            }
            $aliasKey = $caseInsensitive ? mb_strtolower($alias) : $alias;
            $index[$aliasKey] = $index[$targetKey];
        }

        $this->index = $index;
    }

    public static function fromString(string $chars): self
    {
        return new self(self::splitMb($chars));
    }

    /** @param list<string> $chars */
    public static function fromArray(array $chars): self
    {
        return new self(array_values($chars));
    }

    /** @param array<string,string> $foldMap */
    public function withFolding(array $foldMap, bool $caseInsensitive = false): self
    {
        return new self($this->chars, $foldMap, $caseInsensitive);
    }

    public function base(): int
    {
        return $this->base;
    }

    public function zero(): string
    {
        return $this->chars[0];
    }

    public function charAt(int $i): string
    {
        if ($i < 0 || $i >= $this->base) {
            throw new InvalidAlphabetException("Index {$i} out of range for base {$this->base}.");
        }
        return $this->chars[$i];
    }

    public function indexOf(string $char): int
    {
        $key = $this->caseInsensitive ? mb_strtolower($char) : $char;
        if (!isset($this->index[$key])) {
            throw new InvalidAlphabetException("Character '{$char}' is not in alphabet.");
        }
        return $this->index[$key];
    }

    public function equals(self $other): bool
    {
        return $this->chars === $other->chars
            && $this->foldMap === $other->foldMap
            && $this->caseInsensitive === $other->caseInsensitive;
    }

    /** @return list<string> */
    public function chars(): array
    {
        return $this->chars;
    }

    /** @return list<string> */
    private static function splitMb(string $s): array
    {
        if ($s === '') {
            return [];
        }
        return mb_str_split($s);
    }
}
