<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class CloneCommand extends Command
{
    protected string $pattern = "/^(.*)[\/:](.*)\/(.*)$/i";

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clone
        {url : The URL pointing to the repository to clone.}
        {--d|debug : Log the input, regexp pattern, and output of `preg_match`.}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Clone a repository';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            [$url, $host, $account, $repo] = $this->matches();
        } catch (\Throwable) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    protected function matches(): array
    {
        // `preg_match()`, Y U NO TAKE ARRAY VAR FOR `$matches`?? (╯°□°)╯︵ ┻━┻
        $matches = null;

        if($this->option('debug')) {
            $this->info("Running in debug mode!\n");
            $this->line("Received input: {$this->argument('url')}");
            $this->line("Pattern: {$this->pattern}\n");
        };

        /**
         * Pattern doesn't include 'git@' or 'https://' prefix because this is intended to work with shorthands as well.
         */
        if(!preg_match($this->pattern, $this->argument('url'), $matches)) {
            $this->error("URL string does not match expected pattern.");
            $this->error("Pattern: \<host\>(':' or '/')\<account\>/\<repository\>");

            // Force throwing an error, stopping execution in `try`
            return [];
        }

        if($this->option('debug')) {
            $this->info("Matches found:");
            $this->table(
                ['url', 'host', 'account', 'repo'],
                [$matches]
            );

            // Force throwing an error, stopping execution in `try`
            return [];
        }

        return $matches;
    }
}
