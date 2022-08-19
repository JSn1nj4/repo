<?php

namespace App\DTOs;

final class ShortRepoURL
{
    public function __construct(
        public readonly string $original = '',
        public readonly string $host = '',
        public readonly string $account = '',
        public readonly string $repo = ''
    ) {}

    public function __toString(): string
    {
        return print_r($this, true);
    }

    public static function make(string ...$args): self
    {
        return new self(...$args);
    }

    public function toArray(): array
    {
        return [
            'original' => $this->original,
            'host' => $this->host,
            'account' => $this->account,
            'repo' => $this->repo,
        ];
    }
}
