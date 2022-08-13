<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\RemoteSource
 *
 * @property int $id
 * @property string $name
 * @property string $url_base
 * @property string $separator
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Owner[] $owners
 * @property-read int|null $owners_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Repo[] $repos
 * @property-read int|null $repos_count
 * @method static \Database\Factories\RemoteSourceFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource whereSeparator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteSource whereUrlBase($value)
 * @mixin \Eloquent
 */
class RemoteSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url_base',
        'separator',
    ];

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(Owner::class);
    }

    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }
}
