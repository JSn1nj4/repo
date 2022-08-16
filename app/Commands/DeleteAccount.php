<?php

namespace App\Commands;

use App\Enums\AccountSearchableField;
use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelZero\Framework\Commands\Command;

class DeleteAccount extends Command
{
    /**
     * @var Account
     */
    protected Account $account;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'delete:account
        {--search-by=name : The field to find an account by - one of "id", "name", "slug", "shorthand".}
        {search-value : The value to search by.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove an account from the database.';

    /**
     * Check if the found account has dependencies
     *
     * @return bool
     */
    protected function accountHasDependencies(): bool
    {
        $repos_count = $this->account->repos->count();

        if($repos_count === 0) return false;

        $this->error(sprintf(
            "Account '%s' cannot be deleted. There "
            . ($repos_count > 1 ? "are " : "is ")
            . "%d repo" . ($repos_count > 1 ? "s" : "")
            . " registered as "
            . ($repos_count > 1 ? "dependencies." : "as dependency."),
            $this->account->{$this->option('search-by')},
            $repos_count
        ));

        return true;
    }

    /**
     * Check whether an account exists
     *
     * @return bool
     */
    protected function accountIsFound(): bool
    {
        try {
            $this->account = Account::where(
                $this->option('search-by'),
                $this->argument('search-value')
            )->with('repos')
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error(sprintf(
                "An account with '%s' of '%s' was not found.",
                $this->option('search-by'),
                $this->argument('search-value')
            ));

            return false;
        }

        return true;
    }

    protected function deleteAccount(): bool
    {
        try {
            $this->account->deleteOrFail();
        } catch (\Throwable) {
            $this->error(sprintf(
                "Failed to delete account by '%s' with value '%s'.",
                $this->option('search-by'),
                $this->argument('search-value')
            ));

            return false;
        }

        return true;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if(!$this->searchFieldIsAllowed()) return self::FAILURE;

        if(!$this->accountIsFound()) return self::FAILURE;

        if($this->accountHasDependencies()) return self::FAILURE;

        if(!$this->deleteAccount()) return self::FAILURE;

        $this->info(sprintf(
            "Account '%s' deleted.",
            $this->argument('search-value')
        ));

        return self::SUCCESS;
    }

    /**
     * Determine if the search-by field is searchable
     *
     * @return bool
     */
    protected function searchFieldIsAllowed(): bool
    {
        if(!AccountSearchableField::tryFrom($this->option('search-by'))) {
            $this->error(sprintf(
                "'--search-by' must be one of '%s'.",
                AccountSearchableField::implode("', '")
            ));

            return false;
        }

        return true;
    }
}
