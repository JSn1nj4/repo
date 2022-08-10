<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RemoteSource extends Model
{
    use HasFactory;

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(Owner::class);
    }

    public function repos(): HasMany
    {
        return $this->hasMany(Repo::class);
    }
}
