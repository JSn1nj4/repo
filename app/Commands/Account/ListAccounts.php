<?php

namespace App\Commands\Account;

use App\Models\Account;
use App\Models\Host;
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
            ['id', 'host', 'name', 'slug', 'shorthand', 'repos_count'],
            Account::select('id')
                ->addSelect([
                    'host' => Host::select('name')
                        ->whereColumn('id', 'accounts.host_id')
                ])
                ->addSelect(['name', 'slug', 'shorthand'])
                ->withCount('repos')
                ->get()
                ->toArray()
        );

        return self::SUCCESS;
    }
}
