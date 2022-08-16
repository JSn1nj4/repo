<?php

namespace App\Commands\Remote;

use App\Enums\RemoteSourceEditableField;
use App\Enums\RemoteSourceUniqueField;
use App\Traits\CommandFindsRemote;
use LaravelZero\Framework\Commands\Command;

class EditRemoteSource extends Command
{
    use CommandFindsRemote;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'edit:remote_source
        {--search-by=name : The field to find a remote by - one of "id", "name", or "url_base".}
        {--edit-field=name : The field to update - one of "name", "url_base", or "separator".}
        {search-value : The value to search by.}
        {new : The field\'s new value.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit an existing remote source.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if(!$this->searchFieldIsAllowed()) return self::FAILURE;

        if(!$this->fieldIsEditable()) return self::FAILURE;

        if(!$this->findRemote()) return self::FAILURE;

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
     * Update the found remote source
     *
     * @return void
     */
    protected function updateRemoteSource(): void
    {
        $this->remote->{$this->option('edit-field')} = $this->argument('new');
        $this->remote->save();

        $this->info('Remote source updated');
        $this->table(
            ['id', 'name', 'url_base', 'separator'],
            [[
                'id' => $this->remote->id,
                'name' => $this->remote->name,
                'url_base' => $this->remote->url_base,
                'separator' => $this->remote->separator,
            ]]
        );
    }
}
