<?php

namespace App\Commands;

use App\Actions\Console\CheckAccountExists;
use App\Actions\Console\CheckRemoteSourceMissing;
use App\Models\Account;
use App\Models\RemoteSource;
use LaravelZero\Framework\Commands\Command;

class AddAccount extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'add:account
        {name : The name of the account to add.}
        {slug : The url-friendly representation of the account.}
        {shorthand : The shorthand to be used for short URL operations.}
        {remote : The remote source this account is associated with.}
        {--associate-by=id : The field to use for finding the right remote source. Can be one of "id", "name", "url_prefix".}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a new repository account';

    /**
     * Execute the console command.
     *
     * @param CheckAccountExists $ownerExists
     * @param CheckRemoteSourceMissing $remoteSourceMissing
     * @return int
     */
    public function handle(CheckAccountExists $ownerExists, CheckRemoteSourceMissing $remoteSourceMissing): int
    {
        if($ownerExists(
            by: 'name',
            with: $this->argument('name'),
            command: $this
        )) return self::FAILURE;

        if($ownerExists(
            by: 'slug',
            with: $this->argument('slug'),
            command: $this
        )) return self::FAILURE;

        if($remoteSourceMissing(
            by: $this->option('associate-by'),
            with: $this->argument('remote'),
            command: $this
        )) return self::FAILURE;

        Account::create([
            'remote_source_id' => RemoteSource::firstWhere(
                $this->option('associate-by'),
                $this->argument('remote')
            )->id,
            'name' => $this->argument('name'),
            'slug' => $this->argument('slug'),
            'shorthand' => $this->argument('shorthand')
        ]);

        $this->info('Account added successfully.');

        return self::SUCCESS;
    }
}
