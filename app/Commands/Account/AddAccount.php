<?php

namespace App\Commands\Account;

use App\Models\Account;
use App\Models\Host;
use App\Traits\CommandFindsAccount;
use App\Traits\CommandFindsHost;
use LaravelZero\Framework\Commands\Command;

class AddAccount extends Command
{
    use CommandFindsAccount,
        CommandFindsHost;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'add:account
        {name : The name of the account to add.}
        {slug : The url-friendly representation of the account.}
        {shorthand : The shorthand to be used for short URL operations.}
        {host : The host this account is associated with.}
        {--associate-by=id : The field to use for finding the right host. Can be one of "id", "name", "url_prefix", or "shorthand".}';

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
        if(!$this->findHostBy(
            by: $this->option('associate-by'),
            with: $this->argument('host')
        )) {
            $this->error(sprintf(
                "Host with key '%s' matching '%s' does not exist. Would you like to create it?",
                $this->option('associate-by'),
                $this->argument('host')
            ));

            return self::FAILURE;
        }

        if($this->searchAccountWith([
            'name' => $this->argument('name'),
            'slug' => $this->argument('slug'),
            'shorthand' => $this->argument('shorthand'),
        ], $this->argument('host'))) {
            $this->error(sprintf(
                "A matching account was found for host '%s'",
                $this->argument('host')
            ));

            return self::FAILURE;
        }

        Account::create([
            'host_id' => $this->host->id,
            'name' => $this->argument('name'),
            'slug' => $this->argument('slug'),
            'shorthand' => $this->argument('shorthand')
        ]);

        $this->info('Account added successfully.');

        return self::SUCCESS;
    }
}
