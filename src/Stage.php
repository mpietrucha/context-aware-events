<?php

namespace Mpietrucha\Events;

use Closure;
use Composer\Script\Event;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Events\Context;
use Mpietrucha\Support\File;
use Mpietrucha\Support\Process as ProcessBuilder;
use Symfony\Component\Process\Process;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Exception\InvalidArgumentException;

class Stage
{
    use HasFactory;

    protected const STUB = 'stubs/stage.stub.php';

    public function __construct(protected string $event, protected mixed $creator = null)
    {
    }

    public function handle(Closure $callback): ?Context
    {
        if ($this->creator instanceof Event && $this->process()) {
            return null;
        }

        throw_unless($this->creator === null || $this->creator instanceof Closure, new InvalidArgumentException(
            'Closure or null are valid callbacks'
        ));

        return $callback($this->event, $this->creator);
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
