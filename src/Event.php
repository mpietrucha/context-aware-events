<?php

namespace Mpietrucha\Events;

use Mpietrucha\Cli\Cli;
use Mpietrucha\Support\Artisan;
use Mpietrucha\Support\SerializableClosure;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\EventInterface;

abstract class Event implements EventInterface
{
    use HasFactory;

    protected ?SerializableClosure $process = null;

    public function run(Callback $callback): void
    {
        $this->process = SerializableClosure::create(fn () => $callback->assertProcessContext());

        $this->handle();
    }

    public function cli(): Cli
    {
        $this->process();

        return Cli::create();
    }

    public function artisan(string $command, array $arguments = []): void
    {
        $this->process();

        Artisan::call($command, $arguments);
    }

    protected function process(): void
    {
        value($this->process?->getClosure());
    }
}
