<?php

namespace App\Enums\Traits;

trait CanGetValueArray
{
    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }

    public static function implode(string $separator): string
    {
        return implode($separator, static::toArray());
    }
}
