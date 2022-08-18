<?php

namespace App\Traits;

use App\Models\Repo;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CommandFindsRepo
{
    protected Repo $repo;

    protected function repoExists(string $by, string $with): bool
    {
        if(Repo::exists($by, $with)) return true;

        $this->error(sprintf(
            "A repo with '%s' matching '%s' does not exist.",
            $by, $with
        ));

        return false;
    }

    protected function repoMissing(string $by, string $with): bool
    {
        if(!Repo::exists($by, $with)) return true;

        $this->error(sprintf(
            "A repo with '%s' matching '%s' exists.",
            $by, $with
        ));

        return false;
    }

    protected function findRepo(): bool
    {
        try {
            $this->repo = Repo::where(
                $this->option('search-by'),
                $this->argument('search-value')
            )->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error(sprintf(
                "A repo with '%s' matching '%s' does not exist.",
                $this->option('search-by'),
                $this->argument('search-value')
            ));

            return false;
        }

        return true;
    }
}
