<?php

namespace App\Models;

use App\Enums\RemoteSourceUniqueField;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\RemoteSource
 *
 * @property int $id
 * @property string $name
 * @property string $url_base
 * @property string $separator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Account[] $owners
 * @property-read int|null $owners_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Repo[] $repos
 * @property-read int|null $repos_count
 * @method static \Database\Factories\HostFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Host newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Host newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Host query()
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereSeparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Host whereUrlBase($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Account[] $accounts
 * @property-read int|null $accounts_count
 */
class Host extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url_base',
        'separator',
    ];

    /**
     * @param RemoteSourceUniqueField|string $by
     * @param int|string $with
     * @throws \InvalidArgumentException
     * @return bool
     */
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
