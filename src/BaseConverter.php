<?php

declare(strict_types=1);

namespace Anybase;

final class BaseConverter
{
    private readonly Codec $source;
    private readonly Codec $target;

    public function __construct(Alphabet $from, Alphabet $to)
    {
        $this->source = Codec::for($from);
        $this->target = Codec::for($to);
    }

    public function convert(string $value): string
    {
        $numeric = $this->source->decode($value);
        return $this->target->encode($numeric);
    }
}
