<?php

namespace App\Commands\Repo;

use App\Models\Account;
use App\Models\Repo;
use App\Traits\CommandFindsAccount;
use App\Traits\CommandFindsRepo;
use LaravelZero\Framework\Commands\Command;

class AddRepo extends Command
{
    use CommandFindsAccount,
        CommandFindsRepo;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'add:repo
        {name : The name of the repo to add.}
        {slug : The url-friendly representation of the repo.}
        {account : The account associate the repo with.}
        {--associate-by=id : The field to use for finding the right account. Can be one of "id", "name", "slug", "shorthand".}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add a new repo to the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if(!$this->repoMissing(
            by: 'name',
            with: $this->argument('name')
        )) return self::FAILURE;

        if(!$this->repoMissing(
            by: 'slug',
            with: $this->argument('slug')
        )) return self::FAILURE;

        if(!$this->accountExists(
            by: $this->option('associate-by'),
            with: $this->argument('account')
        )) return self::FAILURE;

        Repo::create([
            'account_id' => Account::firstWhere(
                $this->option('associate-by'),
                $this->argument('account')
            )->id,
            'name' => $this->argument('name'),
            'slug' => $this->argument('slug')
        ]);

        $this->info('Repo added successfully.');

        return self::SUCCESS;
    }
}
