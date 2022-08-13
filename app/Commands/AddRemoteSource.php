<?php

namespace App\Commands;

use App\Models\RemoteSource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelZero\Framework\Commands\Command;

class AddRemoteSource extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'add:remote_source
        {name : The name of the remote host to add.}
        {url_base : The base of the remote URL (e.g. \'git@github.com\').}
        {separator : The separator that comes after the URL base (e.g. \':\' or \'/\').}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a new git remote source';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if($this->remoteSourceIsFound(by: 'name')) return self::FAILURE;

        if($this->remoteSourceIsFound(by: 'url_base')) return self::FAILURE;

        RemoteSource::create([
            'name' => $this->argument('name'),
            'url_base' => $this->argument('url_base'),
            'separator' => $this->argument('separator'),
        ]);

        $this->info('Remote source added successfully.');

        return self::SUCCESS;
    }

    /**
     * Try to find a matching RemoteSource
     *
     * @param string $by
     * @throws \InvalidArgumentException
     * @return bool
     */
    protected function remoteSourceIsFound(string $by): bool
    {
        if(!in_array($by, ['name', 'url_base'])) {
            throw new \InvalidArgumentException("Argument for '\$by' must be one of 'name' or 'url_base'.");
        }

        try {
            RemoteSource::where($by, $this->argument($by))
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            return false;
        }

        $this->error(sprintf(
            "A remote source was found by '%s' with value '%s'.",
            $by,
            $this->argument($by)
        ));

        return true;
    }
}
