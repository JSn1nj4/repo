<?php

namespace App\Models;

use App\Enums\AccountSearchableField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class Account extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'host_id',
        'name',
        'slug',
        'shorthand',
    ];

    /**
     * @return BelongsTo
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    /**
     * @return HasMany
     */
    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }

    /**
     * @param AccountSearchableField|string $by
     * @param int|string $with
     * @return bool
     */
    public static function exists(AccountSearchableField|string $by, int|string $with): bool
    {
        if(is_string($by)) {
            $by = AccountSearchableField::tryFrom($by);
        }

        if(is_null($by)) {
            throw new InvalidArgumentException(sprintf(
                "Argument for '\$by' must be either an instance of '%s' or a string of: '%s'.",
                AccountSearchableField::class,
                AccountSearchableField::implode("', '")
            ));
        }

        try {
            static::where($by->value, $with)->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        return true;
    }

    /**
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    public function scopeSearch(Builder $query, mixed $value): void
    {
        $query->where('name', $value)
            ->orWhere('slug', $value)
            ->orWhere('shorthand', $value);
    }

    /**
     * Search with multiple fields
     *
     * @param Builder $query
     * @param array $values
     * @param bool $matchAll
     * @return void
     * @throws InvalidArgumentException
     */
    public function scopeSearchWith(Builder $query, array $values, bool $matchAll = false): void
    {
        foreach(AccountSearchableField::containsWhich(array_keys($values)) as $key => $allowed) {
            if(!$allowed) {
                throw new InvalidArgumentException(sprintf(
                    "'%s' is not a valid search field. Each search field must be one of '%s'.",
                    $key, AccountSearchableField::implode("', '")
                ));
            }
        }

        $isFirst = true;
        foreach($values as $key => $value) {
            /**
             * If this is an "and" search, use only `where` to require a complete match,
             * otherwise use `orWhere` after first iteration.
             */
            $query->{$matchAll || $isFirst ? 'where' : 'orWhere'}($key, $value);
            $isFirst = false;
        }
    }

    /**
     * @param Builder $query
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function scopeWhereHasHost(Builder $query, string $key, mixed $value): void
    {
        $query->whereHas('host', fn($query) => $query->where($key, $value));
    }

    /**
     * @param Builder $query
     * @param mixed $value
     * @return void
     */
    public function scopeWhereSearchHost(Builder $query, mixed $value): void
    {
        $query->whereHas('host',
            fn($query) => $query->where('id', $value)
                ->orWhere('name', $value)
                ->orWhere('slug', $value)
                ->orWhere('shorthand', $value)
        );
    }
}
