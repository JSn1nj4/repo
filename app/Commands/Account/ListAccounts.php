<?php

namespace App\Commands\Account;

use App\Models\Account;
use App\Models\RemoteSource;
use LaravelZero\Framework\Commands\Command;

class ListAccounts extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'list:account';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all saved accounts.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info("Accounts currently saved");
        $this->table(
            ['id', 'remote_source', 'name', 'slug', 'shorthand', 'repos_count'],
            Account::select('id')
                ->addSelect([
                    'remote_source' => RemoteSource::select('name')
                        ->whereColumn('id', 'accounts.remote_source_id')
                ])
                ->addSelect(['name', 'slug', 'shorthand'])
                ->withCount('repos')
                ->get()
                ->toArray()
        );

        return self::SUCCESS;
    }
}
