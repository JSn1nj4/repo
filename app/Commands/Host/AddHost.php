<?php

namespace App\Commands\Host;

use App\Models\Host;
use App\Traits\CommandFindsHost;
use LaravelZero\Framework\Commands\Command;

class AddHost extends Command
{
    use CommandFindsHost;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'add:host
        {name : The name of the host to add.}
        {url_base : The base of the host URL (e.g. \'git@github.com\').}
        {separator : The separator that comes after the URL base (e.g. \':\' or \'/\').}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a new git host';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        if(!$this->hostMissing(
            by: 'name',
            with: $this->argument('name'),
        )) return self::FAILURE;

        if(!$this->hostMissing(
            by: 'url_base',
            with: $this->argument('url_base'),
        )) return self::FAILURE;

        Host::create([
            'name' => $this->argument('name'),
            'url_base' => $this->argument('url_base'),
            'separator' => $this->argument('separator'),
        ]);

        $this->info('Host added successfully.');

        return self::SUCCESS;
    }
}
