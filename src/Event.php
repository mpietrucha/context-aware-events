<?php

namespace Mpietrucha\Events;

use Mpietrucha\Support\Artisan;
use Mpietrucha\Support\Cli;

abstract class Event implements EventInterface
{
    protected ?Cli $cli = null;

    public function cli(): Cli
    {
        return $this->cli ??= Cli::create();
    }

    public function artisan(string $command, array $arguments): void
    {
        Artisan::call($command, $arguments);
    }
}
