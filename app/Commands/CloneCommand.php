<?php

namespace App\Commands;

use App\DTOs\ShortRepoURL;
use App\Traits\CommandFindsAccount;
use App\Traits\CommandFindsHost;
use App\Traits\CommandFindsRepo;
use LaravelZero\Framework\Commands\Command;

class CloneCommand extends Command
{
    use CommandFindsHost,
        CommandFindsAccount,
        CommandFindsRepo;

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

    protected ShortRepoURL $matches;

    protected function checkHostExists(): bool
    {
        if(!$this->hostExists(by: 'shorthand', with: $this->matches->host)) return false;

        $this->host = Host::firstWhere('shorthand', $this->matches->host);

        return true;
    }

    protected function debug(): int
    {
        $this->info("Running in debug mode!\n");
        $this->line("Received input: {$this->argument('url')}");
        $this->line("Pattern: {$this->pattern}\n");

        if(!$this->matches()) return self::FAILURE;

        $this->info("Matches found:");
        $this->table(
            ['original', 'host', 'account', 'repo'],
            [$this->matches->toArray()]
        );

        return self::SUCCESS;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if($this->option('debug')) return $this->debug();

        if(!$this->matches()) return self::FAILURE;

        if(!$this->task('Check host exists', [$this, 'checkHostExists'])) return self::FAILURE;

        return self::SUCCESS;
    }

    protected function matches(): bool
    {
        // `preg_match()`, Y U NO TAKE ARRAY VAR FOR `$matches`?? (╯°□°)╯︵ ┻━┻
        $matches = null;

        /**
         * Pattern doesn't include 'git@' or 'https://' prefix because this is intended to work with shorthands as well.
         */
        if(!preg_match($this->pattern, $this->argument('url'), $matches)) {
            $this->error("URL does not match expected pattern.");
            $this->error("Pattern: \<host\>(':' or '/')\<account\>/\<repository\>");

            // Force throwing an error, stopping execution in `try`
            return false;
        }

        $this->matches = new ShortRepoURL(...$matches);

        return true;
    }
}
