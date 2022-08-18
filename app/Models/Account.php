<?php

namespace App\Models;

use App\Enums\AccountSearchableField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'name',
        'slug',
        'shorthand',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }

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
}
