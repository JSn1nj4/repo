<?php

namespace App\Commands\Account;

use App\Enums\AccountEditableField;
use App\Enums\AccountSearchableField;
use App\Traits\CommandFindsAccount;
use LaravelZero\Framework\Commands\Command;

class EditAccount extends Command
{
    use CommandFindsAccount;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'edit:account
        {--search-by=name : The field to find an account by - one of "id", "name", "slug", or "shorthand".}
        {--edit-field= : The field to update - one of "name", "shorthand", or "slug".}
        {search-value : The value to search by.}
        {new : The field\'s new value.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit an existing account.';

    protected function fieldIsEditable(): bool
    {
        if(!AccountEditableField::tryFrom($this->option('edit-field'))) {
            $this->error(sprintf(
                "'--edit-field' must be one of: '%s'",
                AccountEditableField::implode("', '")
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

        if(!$this->fieldIsEditable()) return self::FAILURE;

        if(!$this->findAccount()) return self::FAILURE;

        $this->updateAccount();

        return self::SUCCESS;
    }

    /**
     * Check if the search field is supported
     *
     * @return bool
     */
    protected function searchFieldIsAllowed(): bool
    {
        if(!AccountSearchableField::tryFrom($this->option('search-by'))) {
            $this->error(sprintf(
                "'--search-by' must be one of : '%s'.",
                AccountSearchableField::implode("', '")
            ));

            return false;
        }

        return true;
    }

    protected function updateAccount(): void
    {
        $this->account->{$this->option('edit-field')} = $this->argument('new');
        $this->account->save();

        $this->info('Account updated.');
        $this->table(
            ['id', 'name', 'slug', 'shorthand'],
            [[
                'id' => $this->account->id,
                'name' => $this->account->name,
                'slug' => $this->account->slug,
                'shorthand' => $this->account->shorthand,
            ]]
        );
    }
}
