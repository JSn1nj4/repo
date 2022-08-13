<?php

namespace App\Commands;

use App\Enums\RemoteSourceUniqueField;
use App\Models\RemoteSource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelZero\Framework\Commands\Command;

class DeleteRemoteSource extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'delete:remote_source
        {--search-by=name : The field to find a remote source by - one of "id", "name", or "url_base".}
        {searchValue : The value to search by.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a remote source from the database.';

    /**
     * The RemoteSource model to delete
     *
     * @var RemoteSource
     */
    protected RemoteSource $remoteSource;

    /**
     * Delete the remote source
     *
     * If unable to delete the remote source, an error will be printed to the console.
     *
     * @return bool
     */
    protected function deleteRemoteSource(): bool
    {
        try {
            $this->remoteSource->deleteOrFail();
        } catch (\Throwable) {
            $this->error(sprintf(
                "Failed to delete remote source by '%s' with value '%s'.",
                $this->option('search-by'),
                $this->argument('searchValue')
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

        if(!$this->remoteSourceIsFound()) return self::FAILURE;

        if($this->remoteSourceHasDependencies()) return self::FAILURE;

        if(!$this->deleteRemoteSource()) return self::FAILURE;

        $this->info(sprintf(
            "Remote source '%s' deleted.",
            $this->argument('searchValue')
        ));

        return self::SUCCESS;
    }

    /**
     * Determine if a remote source still has dependencies
     *
     * @return bool
     */
    protected function remoteSourceHasDependencies(): bool
    {
        // Return early if there are no dependencies
        if($this->remoteSource->owners_count + $this->remoteSource->repos_count === 0) {
            return false;
        }

        // prep counts and error fragments
        $numbers = [];
        $number_errors = [];

        if($this->remoteSource->owners_count > 0) {
            $numbers[] = $this->remoteSource->owners_count;
            $number_errors[] = "%d owners";
        }

        if($this->remoteSource->repos_count > 0) {
            $numbers[] = $this->remoteSource->repos_count;
            $number_errors[] = "%d repos";
        }

        // format final error message
        $this->error(sprintf(
            "Remote source '%s' cannot be deleted. There are "
            . implode(" and ", $number_errors)
            . " registered as dependencies.",
            ...$numbers
        ));

        return true;
    }

    /**
     * Try to find a matching RemoteSource
     *
     * @return bool
     */
    protected function remoteSourceIsFound(): bool
    {
        try {
            $this->remoteSource = RemoteSource::where($this->option('search-by'), $this->argument('searchValue'))
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            $this->error(sprintf(
                "A remote source was not found by '%s' with value '%s'.",
                $this->option('search-by'),
                $this->argument('searchValue')
            ));
            return false;
        }

        return true;
    }

    /**
     * Determine if the search-by field is searchable
     *
     * "Searchable" in this context means for the purposes of this function. Since the "separator" field is not unique, it's possible to accidentally update multiple records if searching by this field.
     *
     * @return bool
     */
    protected function searchFieldIsAllowed(): bool
    {
        if(!RemoteSourceUniqueField::tryFrom($this->option('search-by'))) {
            $this->error(sprintf(
                "'--search-by' must be one of: '%s'",
                RemoteSourceUniqueField::implode('\', \'')
            ));
            return false;
        }

        return true;
    }
}
