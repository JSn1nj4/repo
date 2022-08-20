<?php

namespace App\Traits;

use App\Models\Host;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CommandFindsHost
{
    protected Host $host;

    protected function findHostBy(string $by, mixed $with): bool
    {
        try {
            $this->host = Host::where($by, $with)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        return true;
    }

    protected function hostExists(string $by, string $with): bool
    {
        if(Host::exists($by, $with)) return true;

        $this->error(sprintf(
            "A host with '%s' matching '%s' was not found.",
            $by, $with
        ));

        return false;
    }

    protected function hostMissing(string $by, string $with): bool
    {
        if(!Host::exists($by, $with)) return true;

        $this->error(sprintf(
            "A host with '%s' matching '%s' exists.",
            $by, $with
        ));

        return false;
    }

    protected function findHost(): bool
    {
        try {
            $this->host = Host::where(
                $this->option('search-by'),
                $this->argument('search-value')
            )->with(['accounts', 'repos'])
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error(sprintf(
                "A host was not found by '%s' with value '%s'.",
                $this->option('search-by'),
                $this->argument('search-value')
            ));

            return false;
        }

        return true;
    }
}
