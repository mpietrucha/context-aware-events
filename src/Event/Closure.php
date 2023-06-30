<?php

namespace Mpietrucha\Events\Event;

use Mpietrucha\Cli\Cli;
use Mpietrucha\Support\Artisan;
use Mpietrucha\Events\Callback;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\EventInterface;

abstract class Closure implements EventInterface
{
    use HasFactory;

    abstract public function handle(): void;

    public function run(Callback $callback): void
    {
        $this->before($callback);

        $this->handle();

        $this->cli()->newLine();
    }

    public function before(Callback $callback): void
    {
        $callback->assertClosureContext();
    }

    public function cli(): Cli
    {
        return Cli::create();
    }

    public function artisan(string $command, array $arguments = []): void
    {
        Artisan::call($command, $arguments);
    }
}
