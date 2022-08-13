<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RemoteSource[] $remoteSources
 * @property-read int|null $remote_sources_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Repo[] $repos
 * @property-read int|null $repos_count
 * @method static \Database\Factories\OwnerFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Owner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Owner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereShorthand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Owner whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Owner extends Model
{
    use HasFactory;

    public function remoteSources(): BelongsToMany
    {
        return $this->belongsToMany(RemoteSource::class);
    }

    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }
}
