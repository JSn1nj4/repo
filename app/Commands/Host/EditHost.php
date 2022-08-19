<?php

namespace App\Commands\Host;

use App\Enums\HostEditableField;
use App\Enums\HostUniqueField;
use App\Traits\CommandFindsHost;
use LaravelZero\Framework\Commands\Command;

class EditHost extends Command
{
    use CommandFindsHost;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'edit:host
        {--search-by=name : The field to find a host by - one of "id", "name", "url_base", or "shorthand".}
        {--edit-field=name : The field to update - one of "name", "url_base", "separator", or "shorthand".}
        {search-value : The value to search by.}
        {new : The field\'s new value.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit an existing host.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if(!$this->searchFieldIsAllowed()) return self::FAILURE;

        if(!$this->fieldIsEditable()) return self::FAILURE;

        if(!$this->findHost()) return self::FAILURE;

        $this->updateHost();

        return self::SUCCESS;
    }

    /**
     * Determine if the "edit" field is editable
     *
     * @return bool
     */
    protected function fieldIsEditable(): bool
    {
        if(!HostEditableField::tryFrom($this->option('edit-field'))) {
            $this->error(sprintf(
                "'--edit-field' must be one of: '%s'",
                HostEditableField::implode('\', \'')
            ));
            return false;
        }

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

    protected function updateHost(): void
    {
        $this->host->{$this->option('edit-field')} = $this->argument('new');
        $this->host->save();

        $this->info('Host updated');
        $this->table(
            ['id', 'name', 'url_base', 'separator', 'shorthand'],
            [[
                'id' => $this->host->id,
                'name' => $this->host->name,
                'url_base' => $this->host->url_base,
                'separator' => $this->host->separator,
                'shorthand' => $this->host->shorthand,
            ]]
        );
    }
}
