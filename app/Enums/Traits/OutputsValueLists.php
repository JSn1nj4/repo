<?php

namespace App\Enums\Traits;

/**
 * Utilities for returning all case values in various formats
 */
trait OutputsValueLists
{
    public static function implode(string $separator): string
    {
        return implode($separator, static::caseValues());
    }

    public static function caseValues(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }
}
