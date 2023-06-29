<?php

namespace Mpietrucha\Events;

use Mpietrucha\Support\Artisan;
use Mpietrucha\Support\Cli;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\EventInterface;

abstract class Event implements EventInterface
{
    use HasFactory;

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
