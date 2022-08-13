<?php

namespace App\Enums\Traits;

/**
 * Utilities for returning all case values in various formats
 */
trait OutputsValueLists
{
    /**
     * Return all values as a delimited list
     *
     * @param string $separator
     * @return string
     */
    public static function implode(string $separator): string
    {
        return implode($separator, static::caseValues());
    }

    /**
     * Returns an array containing all case values
     *
     * @return array
     */
    public static function caseValues(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }
}
