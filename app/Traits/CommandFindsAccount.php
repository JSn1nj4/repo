<?php

namespace App\Traits;

use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CommandFindsAccount
{
    protected Account $account;

    protected function accountExists(string $by, string $with): bool
    {
        if(Account::exists($by, $with)) return true;

        $this->error(sprintf(
            "An account with '%s' matching '%s' was not found.",
            $by, $with
        ));

        return false;
    }

    protected function accountMissing(string $by, string $with): bool
    {
        if(!Account::exists($by, $with)) return true;

        $this->error(sprintf(
            "An account with '%s' matching '%s' exists.",
            $by, $with
        ));

        return false;
    }

    protected function findAccount(): bool
    {
        try {
            $this->account = Account::where(
                $this->option('search-by'),
                $this->argument('search-value')
            )->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error(sprintf(
                "An account with '%s' matching '%s' does not exist.",
                $this->option('search-by'),
                $this->argument('search-value')
            ));

            return false;
        }

        return true;
    }
}
