<?php

namespace Mpietrucha\Events;

use Closure;
use Composer\Script\Event;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Events\Context;
use Mpietrucha\Support\File;
use Mpietrucha\Events\Contracts\EventInterface;
use Mpietrucha\Support\Process as ProcessBuilder;
use Symfony\Component\Process\Process;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Exception\InvalidArgumentException;

class Stage
{
    use HasFactory;

    public function __construct(protected string $event, protected mixed $creator = null)
    {
    }

    public function handle(Closure $callback): ?Context
    {
        $creator = $this->creator;

        if ($creator instanceof Event && $this->process()) {
            return null;
        }

        if ($creator instanceof EventInterface) {
            $creator = fn () => $creator->handle();
        }

        throw_unless($creator === null || $creator instanceof Closure, new InvalidArgumentException(
            'Dispatcher should be called without any arguments, event valid callbacks are', [Closure::class], 'or', [EventInterface::class]
        ));

        return $callback($this->event, $creator);
    }

    protected function process(): bool
    {
        $autoload = $this->creator->getComposer()->getConfig()->get('vendor-dir') . DIRECTORY_SEPARATOR . Vendor::AUTOLOAD;

        require_once $autoload;

        $event = $this->event;

        $command = "require_once '$autoload'; \Mpietrucha\Events\Component\Dispatcher::$event();";

        if (! $command = ProcessBuilder::php($command)->buildCommandWithExecutable()) {
            return false;
        }

        $process = new Process($command);

        $process->setTty(true)->run();

        return true;
    }
}
