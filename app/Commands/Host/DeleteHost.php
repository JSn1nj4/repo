<?php

namespace App\Commands\Host;

use App\Enums\HostUniqueField;
use App\Traits\CommandFindsHost;
use LaravelZero\Framework\Commands\Command;

class DeleteHost extends Command
{
    use CommandFindsHost;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'delete:host
        {--search-by=name : The field to find a remote source by - one of "id", "name", or "url_base".}
        {search-value : The value to search by.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Remove a remote source from the database.';

    protected function deleteRemoteSource(): bool
    {
        try {
            $this->host->deleteOrFail();
        } catch (\Throwable) {
            $this->error(sprintf(
                "Failed to delete remote source by '%s' with value '%s'.",
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

        if(!$this->findHost()) return self::FAILURE;

        if($this->remoteSourceHasDependencies()) return self::FAILURE;

        if(!$this->deleteRemoteSource()) return self::FAILURE;

        $this->info(sprintf(
            "Remote '%s' deleted.",
            $this->argument('search-value')
        ));

        return self::SUCCESS;
    }

    protected function remoteSourceHasDependencies(): bool
    {
        $accounts_count = $this->host->accounts->count();
        $repos_count = $this->host->repos->count();

        // Return early if there are no dependencies
        if($accounts_count + $repos_count === 0) return false;

        // prep counts and error fragments
        $numbers = [];
        $number_errors = [];

        if($accounts_count > 0) {
            $numbers[] = $accounts_count;
            $number_errors[] = "%d account" . ($accounts_count > 1 ? "s" : "");
        }

        if($repos_count > 0) {
            $numbers[] = $repos_count;
            $number_errors[] = "%d repo" . ($repos_count > 1 ? "s" : "");
        }

        // format final error message
        $total = array_sum($numbers);

        $this->error(sprintf(
            "Remote '%s' cannot be deleted. There "
            . ($total > 1 ? "are " : "is ")
            . implode(" and ", $number_errors)
            . " registered as "
            . ($total > 1 ? "dependencies." : "a dependency."),
            $this->host->{$this->option('search-by')},
            ...$numbers
        ));

        return true;
    }

    protected function searchFieldIsAllowed(): bool
    {
        if(!HostUniqueField::tryFrom($this->option('search-by'))) {
            $this->error(sprintf(
                "'--search-by' must be one of: '%s'",
                HostUniqueField::implode('\', \'')
            ));
            return false;
        }

        return true;
    }
}
