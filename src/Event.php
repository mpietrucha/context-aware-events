<?php

namespace Mpietrucha\Events;

use Mpietrucha\Cli\Cli;
use Mpietrucha\Support\Artisan;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\EventInterface;

abstract class Event implements EventInterface
{
    use HasFactory;

    public function cli(): Cli
    {
        return Cli::create();
    }

    public function artisan(string $command, array $arguments = []): void
    {
        Artisan::call($command, $arguments);
    }
}
