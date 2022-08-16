<?php

namespace App\Models;

use App\Enums\AccountSearchableField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

/**
 * An "owner" is a repository owner - either a user or an org
 *
 * This is why it's called "Owner", because this tool doesn't know
 * what type of account owns a repository.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $shorthand
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Host[] $remoteSources
 * @property-read int|null $remote_sources_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Repo[] $repos
 * @property-read int|null $repos_count
 * @method static \Database\Factories\OwnerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereShorthand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $remote_source_id
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereRemoteSourceId($value)
 */
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

    /**
     * @param AccountSearchableField|string $by
     * @param int|string $with
     * @return bool
     *@throws InvalidArgumentException
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
}
