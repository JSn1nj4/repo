<?php

namespace App\Commands;

use App\Actions\CheckRemoteSourceExists;
use App\Enums\RemoteSourceEditableField;
use App\Models\RemoteSource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelZero\Framework\Commands\Command;

class EditRemoteSource extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'edit:remote_source
        {--search-by=name : The field to find a remote by - one of "id", "name", or "url_base".}
        {--edit-field=name : The field to update - one of "name", "url_base", or "separator".}
        {searchValue : The value to search by.}
        {newValue : The field\'s new value.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Edit an existing remote source.';

    /**
     * The RemoteSource model to udpate
     *
     * @var RemoteSource
     */
    protected RemoteSource $remoteSource;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CheckRemoteSourceExists $remoteSourceExists)
    {
        if(!$this->searchFieldIsAllowed()) return self::FAILURE;

        if(!$this->fieldIsEditable()) return self::FAILURE;

        if(!$this->remoteSourceIsFound()) return self::FAILURE;

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
        if(!in_array($this->option('search-by'), ['id', 'name', 'url_base'])) {
            $this->error("'--search-by' must be one of: 'id', 'name', 'url_base'");
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
        $this->remoteSource->{$this->option('edit-field')} = $this->argument('newValue');
        $this->remoteSource->save();

        $this->info('Remote source updated');
        $this->table(
            ['id', 'name', 'url_base', 'separator'],
            [[
                'id' => $this->remoteSource->id,
                'name' => $this->remoteSource->name,
                'url_base' => $this->remoteSource->url_base,
                'separator' => $this->remoteSource->separator,
            ]]
        );
    }
}
