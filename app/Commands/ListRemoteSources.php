<?php

namespace App\Commands;

use App\Models\RemoteSource;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ListRemoteSources extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'list:remote_sources';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List all remote sources currently stored.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->info("Remote sources currently saved");
        $this->table(
            ['id', 'name', 'url_base', 'separator', 'owners_count', 'repos_count'],
            RemoteSource::select(['id', 'name', 'url_base', 'separator'])
                ->withCount(['owners', 'repos'])
                ->get()
                ->toArray()
        );

        return self::SUCCESS;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
