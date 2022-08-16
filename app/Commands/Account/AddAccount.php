<?php

namespace App\Commands\Account;

use App\Models\Account;
use App\Models\RemoteSource;
use App\Traits\CommandFindsAccount;
use App\Traits\CommandFindsRemote;
use LaravelZero\Framework\Commands\Command;

class AddAccount extends Command
{
    use CommandFindsAccount,
        CommandFindsRemote;

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
     * @return int
     */
    public function handle(): int
    {
        if(!$this->accountMissing(
            by: 'name',
            with: $this->argument('name')
        )) return self::FAILURE;

        if(!$this->accountMissing(
            by: 'slug',
            with: $this->argument('slug')
        )) return self::FAILURE;

        if(!$this->remoteExists(
            by: $this->option('associate-by'),
            with: $this->argument('remote'),
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
