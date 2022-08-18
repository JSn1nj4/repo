<?php

namespace App\Commands\Repo;

use App\Models\Account;
use App\Models\Repo;
use LaravelZero\Framework\Commands\Command;

class ListRepo extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'list:repo';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all saved repos.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Repos currently saved");
        $this->table(
            ['id', 'account', 'name', 'slug'],
            Repo::select('id')
                ->addSelect([
                    'account' => Account::select('name')
                        ->whereColumn('id', 'repos.account_id')
                ])
                ->addSelect(['name', 'slug'])
                ->get()
                ->toArray()
        );

        return self::SUCCESS;
    }
}
