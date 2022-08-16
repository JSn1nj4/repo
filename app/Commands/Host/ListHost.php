<?php

namespace App\Commands\Host;

use App\Models\Host;
use LaravelZero\Framework\Commands\Command;

class ListHost extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'list:host';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all hosts currently stored.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->info("Hosts currently saved");
        $this->table(
            ['id', 'name', 'url_base', 'separator', 'accounts_count', 'repos_count'],
            Host::select(['id', 'name', 'url_base', 'separator'])
                ->withCount(['accounts', 'repos'])
                ->get()
                ->toArray()
        );

        return self::SUCCESS;
    }
}
