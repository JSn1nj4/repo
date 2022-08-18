<?php

namespace App\Models;

use App\Enums\RemoteSourceUniqueField;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Host extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url_base',
        'separator',
    ];

    public static function exists(RemoteSourceUniqueField|string $by, int|string $with): bool
    {
        if(is_string($by)) {
            $by = RemoteSourceUniqueField::tryFrom($by);
        }

        if(is_null($by)) {
            throw new InvalidArgumentException(sprintf(
                "Argument for '\$by' must be either an instance of '%s' or a string of: '%s'.",
                RemoteSourceUniqueField::class,
                RemoteSourceUniqueField::implode("', '")
            ));
        }

        try {
            static::where($by->value, $with)->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        return true;
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function repos(): HasManyThrough
    {
        return $this->hasManyThrough(Repo::class, Account::class);
    }
}
