<?php

namespace Mpietrucha\Events;

use Mpietrucha\Cli\Cli;
use Mpietrucha\Events\Callback;
use Mpietrucha\Support\Artisan;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\EventInterface;

abstract class Event implements EventInterface
{
    use HasFactory;

    protected Callback $callback;

    public function run(Callback $callback): void
    {
        $this->callback = $callback;

        $this->handle();
    }

    public function cli(): Cli
    {
        $this->callback->assertProcessContext();

        return Cli::create();
    }

    public function artisan(string $command, array $arguments = []): void
    {
        $this->callback->assertProcessContext();

        Artisan::call($command, $arguments);
    }
}
