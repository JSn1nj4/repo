<?php

namespace App\Commands;

use App\Actions\CheckRemoteSourceExists;
use App\Models\RemoteSource;
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
    public function handle(CheckRemoteSourceExists $remoteSourceExists): int
    {
        if($remoteSourceExists($this->argument('name'))) {
            $this->error("A remote source named '{$this->argument('name')}' already exists.");
            return self::FAILURE;
        }

        if($remoteSourceExists(url_base: $this->argument('url_base'))) {
            $this->error("A remote source exists with url_base '{$this->argument('url_base')}'.");
            return self::FAILURE;
        }

        RemoteSource::create([
            'name' => $this->argument('name'),
            'url_base' => $this->argument('url_base'),
            'separator' => $this->argument('separator'),
        ]);

        $this->info('Remote source added successfully.');

        return self::SUCCESS;
    }
}
