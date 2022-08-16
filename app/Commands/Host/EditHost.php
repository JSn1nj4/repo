<?php

namespace App\Commands\Host;

use App\Enums\RemoteSourceEditableField;
use App\Enums\RemoteSourceUniqueField;
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
        {--search-by=name : The field to find a host by - one of "id", "name", or "url_base".}
        {--edit-field=name : The field to update - one of "name", "url_base", or "separator".}
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

        $this->updateRemoteSource();

        return self::SUCCESS;
    }

    /**
     * Determine if the "edit" field is editable
     *
     * @return bool
     */
    protected function fieldIsEditable(): bool
    {
        if(!RemoteSourceEditableField::tryFrom($this->option('edit-field'))) {
            $this->error(sprintf(
                "'--edit-field' must be one of: '%s'",
                RemoteSourceEditableField::implode('\', \'')
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

    /**
     * Update the found host
     *
     * @return void
     */
    protected function updateRemoteSource(): void
    {
        $this->host->{$this->option('edit-field')} = $this->argument('new');
        $this->host->save();

        $this->info('Host updated');
        $this->table(
            ['id', 'name', 'url_base', 'separator'],
            [[
                'id' => $this->host->id,
                'name' => $this->host->name,
                'url_base' => $this->host->url_base,
                'separator' => $this->host->separator,
            ]]
        );
    }
}
