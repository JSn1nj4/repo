<?php

namespace App\Models;

use App\Enums\RepoSearchableField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repo extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'name',
        'slug',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public static function exists(RepoSearchableField|string $by, int|string $with): bool
    {
        if(is_string($by)) {
            $by = RepoSearchableField::tryFrom($by);
        }

        if(is_null($by)) {
            throw new \InvalidArgumentException(sprintf(
                "Argument for '\$by' must be either an instance of '%s' or a string of: '%s'.",
                RepoSearchableField::class,
                RepoSearchableField::implode("', '")
            ));
        }

        try {
            static::where($by->value, $with)->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        return true;
    }

    public function remoteSource(): BelongsTo
    {
        return $this->account->belongsTo(Host::class);
    }
}
