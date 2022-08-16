<?php

namespace App\Actions\Console;

use App\Models\RemoteSource;
use LaravelZero\Framework\Commands\Command;

class CheckRemoteSourceMissing extends ConsoleAction
{
    /**
     * Check if a remote source exists given a field and value
     *
     * @param string $by
     * @param string $with
     * @param Command $command
     * @return bool
     */
    public function __invoke(string $by, string $with, Command $command): bool
    {
        if(RemoteSource::exists($by, $with)) return false;

        $command->error(sprintf(
            "A remote source with '%s' matching '%s' was not found.",
            $by,
            $with
        ));

        return true;
    }
}
