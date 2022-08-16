<?php

namespace App\Traits;

use App\Models\RemoteSource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CommandFindsRemote
{
    protected RemoteSource $remote;

    protected function remoteExists(string $by, string $with): bool
    {
        if(RemoteSource::exists($by, $with)) return true;

        $this->error(sprintf(
            "A remote with '%s' matching '%s' was not found.",
            $by, $with
        ));

        return false;
    }

    protected function remoteMissing(string $by, string $with): bool
    {
        if(!RemoteSource::exists($by, $with)) return true;

        $this->error(sprintf(
            "A remote with '%s' matching '%s' exists.",
            $by, $with
        ));

        return false;
    }

    protected function findRemote(): bool
    {
        try {
            $this->remote = RemoteSource::where(
                $this->option('search-by'),
                $this->argument('search-value')
            )->with(['accounts', 'repos'])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error(sprintf(
                "A remote was not found by '%s' with value '%s'.",
                $this->option('search-by'),
                $this->argument('search-value')
            ));

            return false;
        }

        return true;
    }
}
