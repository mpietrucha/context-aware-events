<?php

namespace Mpietrucha\Events;

use Closure;
use Throwable;
use Mpietrucha\Cli\Output;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Support\Base64;
use Mpietrucha\Support\File;
use Mpietrucha\Support\Serializer;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Concerns\HasVendor;
use Mpietrucha\Php\Error\Reporting;
use Mpietrucha\Php\Error\Error;
use Mpietrucha\Support\Rescue;
use Symfony\Component\Process\PhpProcess;
use Mpietrucha\Events\Exception\ClosureNotAllowedException;
use Mpietrucha\Events\Contracts\OutputConfiguratorInterface;

class Process
{
    use HasFactory;

    use HasVendor;

    protected Callback $callback;

    protected bool $hasWarning = false;

    protected static OutputConfiguratorInterface $configurator;

    protected const STUB = 'stubs/process.stub.php';

    public function __construct(Closure $callback, protected string $caller)
    {
        $this->callback = Callback::create($callback)->unbind();

        $this->output = Output::create()->buffer(function (string $buffer) {
            if (self::$configurator->disable()) {
                return;
            }

            if (self::$configurator->warning() && ! $this->hasWarning) {
                 $this->output->error('Unexpected output from event closure');

                 $this->hasWarning = true;
            }

            return $this->output->{self::$configurator->type()}($buffer);
        });

        $this->output->style()->prefix($this->vendor());

        Reporting::withFreshErrors();

        Reporting::create()->disable()->while(function () use ($callback) {
            Rescue::create(fn () => $callback())->fail($this->process(...))->call();
        });

        Reporting::errors()->each(function (Error $error) {
            $this->output->error($error->error());
        });

        $this->output->end();
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

        $process = new PhpProcess($this->executableContents());

        $process->setTimeout(null)->run();

        if ($output = $process->getErrorOutput()) {
            $this->output->error($output);

            return;
        }

        if (! $output = $process->getOutput()) {
            return;
        }

        str($output)->toNewLineCollection()->each(function (string $output) {
            echo $output;
        });
    }

    protected function executableContents(): string
    {
        return strtr($this->executableStub(), [
            '__AUTOLOAD__' => $this->vendor()->autoload(),
            '__CALLBACK__' => $this->executableCallback()
        ]);
    }

    protected function executableCallback(): string
    {
        $serialized = Serializer::create($this->callback)->serialize();

        return Base64::encode($serialized);
    }

    protected function executableStub(): string
    {
        $stub = collect([$this->vendor()->path(), self::STUB])->toRootDirectory();

        return File::get($stub);
    }
}
