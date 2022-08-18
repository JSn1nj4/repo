<?php

namespace App\Commands\Repo;

use App\Enums\RepoSearchableField;
use App\Traits\CommandFindsRepo;
use LaravelZero\Framework\Commands\Command;

class DeleteRepo extends Command
{
    use CommandFindsRepo;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'delete:repo
        {--search-by=name : The field to find a repo by - one of "id", "name", "slug".}
        {search-value : The value to search by.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a repo from the database.';

    protected function deleteRepo(): bool
    {
        try {
            $this->repo->deleteOrFail();
        } catch (\Throwable) {
            $this->error(sprintf(
                "Failed to delete repo by '%s' with value '%s'.",
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

        if(!$this->findRepo()) return self::FAILURE;

        if(!$this->deleteRepo()) return self::FAILURE;

        $this->info(sprintf(
            "Repo '%s' deleted.",
            $this->argument('search-value')
        ));

        return self::SUCCESS;
    }

    protected function searchFieldIsAllowed(): bool
    {
        if(!RepoSearchableField::tryFrom($this->option('search-by'))) {
            $this->error(sprintf(
                "'--search-by' must be one of '%s'.",
                RepoSearchableField::implode("', '")
            ));

            return false;
        }

        return true;
    }
}
