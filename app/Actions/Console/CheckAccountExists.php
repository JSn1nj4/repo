<?php

namespace App\Actions\Console;

use App\Models\Account;
use LaravelZero\Framework\Commands\Command;

class CheckAccountExists extends ConsoleAction
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
        if(!Account::exists($by, $with)) return false;

        $command->error(sprintf(
            "An account with '%s' matching '%s' exists.",
            $by,
            $with
        ));

        return true;
    }
}
