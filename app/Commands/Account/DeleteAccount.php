<?php

namespace App\Commands\Account;

use App\Enums\AccountSearchableField;
use App\Traits\CommandFindsAccount;
use LaravelZero\Framework\Commands\Command;

class DeleteAccount extends Command
{
    use CommandFindsAccount;

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

        if(!$this->findAccount()) return self::FAILURE;

        if($this->accountHasDependencies()) return self::FAILURE;

        if(!$this->deleteAccount()) return self::FAILURE;

        $this->info(sprintf(
            "Account '%s' deleted.",
            $this->argument('search-value')
        ));

        return self::SUCCESS;
    }

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
