<?php

namespace App\Commands;

use App\Enums\AccountEditableField;
use App\Enums\AccountSearchableField;
use App\Models\Account;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelZero\Framework\Commands\Command;

class EditAccount extends Command
{
    /**
     * The Account model to update
     *
     * @var Account
     */
    protected Account $account;

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

    /**
     * Check if account exists
     *
     * If so, save the reference, otherwise log an error.
     *
     * @return bool
     */
    protected function accountIsFound(): bool
    {
        try {
            $this->account = Account::where(
                $this->option('search-by'),
                $this->argument('search-value')
            )->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error(sprintf(
                "A remote source with '%s' matching '%s' does not exist.",
                $this->option('search-by'),
                $this->argument('search-value')
            ));

            return false;
        }

        return true;
    }

    /**
     * Check if the field to edit is editable
     *
     * @return bool
     */
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

        if(!$this->accountIsFound()) return self::FAILURE;

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
