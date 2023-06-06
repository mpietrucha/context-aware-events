<?php

namespace Mpietrucha\Events;

use Closure;
use Throwable;
use Mpietrucha\Cli\Cli;
use Mpietrucha\Support\Caller;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Support\Base64;
use Mpietrucha\Support\File;
use Mpietrucha\Support\Process as ProcessFactory;
use Mpietrucha\Support\Serializer;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Concerns\HasVendor;
use Mpietrucha\Error\Reporting;
use Mpietrucha\Error\Repository\Error;
use Mpietrucha\Support\Rescue;
use Mpietrucha\Cli\Buffer\Handlers\SymfonyVarDumperHandler;
use Mpietrucha\Events\Exception\ClosureNotAllowedException;
use Mpietrucha\Events\Contracts\OutputConfiguratorInterface;

class Process
{
    use HasFactory;

    use HasVendor;

    protected Callback $callback;

    protected static OutputConfiguratorInterface $configurator;

    protected const STUB = 'stubs/process.stub.php';

    protected const ERRORABLE = 'context.aware.events.process.errors';

    public function __construct(Closure $callback, protected string $caller)
    {
        $this->callback = Callback::create($callback)->unbind();

        $this->output = Cli::create()->buffer(function (Cli $cli) {
            if (self::$configurator->disable()) {
                $this->tty(false);
            }

            if (self::$configurator->warning()) {
                 once(fn () => $cli->error('Unexpected output from event closure'))->call();
            }

            return fn (string $buffer) => $cli->{self::$configurator->type()}($buffer);
        });

        $this->output->style()->type($this->vendor());

        Reporting::create()->errorableAs(self::ERRORABLE)->disable()->while(function () use ($callback) {
            Rescue::create(fn () => $callback())->fail($this->process(...))->call();
        });

        Reporting::errors(self::ERRORABLE)->each(function (Error $error) {
            $this->output->error($error->error());
        });

        $this->output->finish();
    }

    public static function setConfigurator(OutputConfiguratorInterface $configurator): void
    {
        self::$configurator = $configurator;
    }

    protected function process(Throwable $exception): void
    {
        if (! $exception instanceof ClosureNotAllowedException) {
            $this->output->error($exception);

            return;
        }

        $this->callback->framework(in: $this->caller);

        $this->callback->runningInProcessMode();

        $process = ProcessFactory::file($this->executableStub())->stub([
            '__AUTOLOAD__' => $this->vendor()->autoload(),
            '__CALLBACK__' => $this->executableCallback(),
            '__COLORS__' => $this->output->getBuffer()->handlers()->get(SymfonyVarDumperHandler::class)->getSupportsColors()
        ])->forever()->run();

        if ($output = $process->errorOutput()) {
            $this->output->error($output);

            return;
        }

        if (! $output = $process->output()) {
            return;
        }

        str($output)->toNewLineCollection()->each(function (string $output) {
            echo $output;
        });
    }

    protected function executableCallback(): string
    {
        $serialized = Serializer::create($this->callback)->serialize();

        return Base64::encode($serialized);
    }

    protected function executableStub(): string
    {
        return collect([$this->vendor()->path(), self::STUB])->toRootDirectory();
    }
}
