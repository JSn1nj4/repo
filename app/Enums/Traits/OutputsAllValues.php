<?php

namespace App\Enums\Traits;

trait OutputsAllValues
{
    public static function caseValues(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }

    public static function implode(string $separator): string
    {
        return implode($separator, static::caseValues());
    }
}
