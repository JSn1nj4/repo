<?php

namespace App\Traits;

use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait CommandFindsAccount
{
    protected Account $account;

    protected function findAccountBy(string $by, string $with, string $host_key, string $host_value): bool
    {
        try {
            $this->account = Account::where($by, $with)
                ->whereHasHost($host_key, $host_value)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        return true;
    }

    protected function searchAccount(mixed $value, mixed $host_value): bool
    {
        try {
            $this->account = Account::search($value)
                ->whereSearchHost($host_value)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        return true;
    }

    protected function searchAccountWith(array $fields, mixed $host_value): bool
    {
        try {
            $this->account = Account::searchWith($fields)
                ->whereSearchHost($host_value)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        return true;
    }
}
