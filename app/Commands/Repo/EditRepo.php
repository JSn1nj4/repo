<?php

namespace App\Commands\Repo;

use App\Enums\RepoEditableField;
use App\Enums\RepoSearchableField;
use App\Traits\CommandFindsRepo;
use LaravelZero\Framework\Commands\Command;

class EditRepo extends Command
{
    use CommandFindsRepo;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'edit:repo
        {--search-by=name : The field to find a repo by - one of "id", "name", "slug".}
        {--edit-field= : The field to update - one of "name", "slug".}
        {search-value : The value to search by.}
        {new : The field\'s new value.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit an existing repo.';

    protected function fieldIsEditable(): bool
    {
        if(!RepoEditableField::tryFrom($this->option('edit-field'))) {
            $this->error(sprintf(
                "'--edit-field' must be one of : '%s'.",
                RepoEditableField::implode("', '")
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

        if(!$this->findRepo()) return self::FAILURE;

        $this->updateRepo();

        return self::SUCCESS;
    }

    protected function searchFieldIsAllowed(): bool
    {
        if(!RepoSearchableField::tryFrom($this->option('search-by'))) {
            $this->error(sprintf(
                "'--search-by' must be one of: '%s'.",
                RepoSearchableField::implode("', '")
            ));

            return false;
        }

        return true;
    }

    protected function updateRepo(): void
    {
        $this->repo->{$this->option('edit-field')} = $this->argument('new');
        $this->repo->save();

        $this->info('Repo updated.');
        $this->table(
            ['id', 'name', 'slug'],
            [[
                'id' => $this->repo->id,
                'name' => $this->repo->name,
                'slug' => $this->repo->slug,
            ]]
        );
    }
}
