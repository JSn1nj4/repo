<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CloneCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clone {url}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Clone a repository';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Cloning '" . $this->argument('url') . "'...");

        return self::SUCCESS;
    }
}
