<?php

namespace App\Enums\Traits;

trait ChecksValues
{
    /**
     * Check that a supplied value exists as an enum case
     *
     * @param mixed $value
     * @return bool
     */
    public static function contains(mixed $value): bool
    {
        return !!match($value) {
            is_object($value) => static::tryFrom($value::class),
            default => static::tryFrom($value)
        };
    }

    /**
     * Checks if any supplied values match existing cases
     *
     * @param array $values
     * @return bool
     */
    public static function containsAny(array $values): bool
    {
        foreach($values as $value) {
            if(static::contains($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks which values are existing cases
     *
     * Supplied values must have a method of simple string representation (e.g. a __toString() method or $object::class).
     *
     * @param array $values
     * @return array
     */
    public static function containsWhich(array $values): array
    {
        return array_reduce($values, fn($cases, $item) => $cases + match($item) {
            is_object($item) => [$item::class => static::contains($item)],
            is_numeric($item),
                is_null($item),
                is_bool($item) => ["$item" => static::contains($item)],
            default => [$item => static::contains($item)]
        }, []);
    }
}
