<?php

namespace Mpietrucha\Events;

use Closure;
use Throwable;
use Mpietrucha\Cli\Cli;
use Mpietrucha\Support\Base64;
use Mpietrucha\Support\Caller;
use Mpietrucha\Support\Rescue;
use Mpietrucha\Error\Reporting;
use Mpietrucha\Support\Serializer;
use Mpietrucha\Support\Concerns\HasVendor;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Process as ProcessFactory;
use Mpietrucha\Events\Exception\ClosureNotAllowedException;

class Process
{
    use HasFactory;

    use HasVendor;

    protected Cli $output;

    protected Closure $outputer;

    protected Callback $callback;

    protected static Closure $configurator;

    protected const STUB = 'stubs/process.stub.php';

    public function __construct(Closure $callback, protected string $caller)
    {
        $this->callback = Callback::create($callback)->unbind();

        [$configurator, $vendor] = [self::$configurator, $this->vendor()];

        $this->outputer = fn () => tap(Cli::create()->withErrorHandler()->buffer($configurator), function (Cli $cli) use ($vendor) {
            $cli->style()->as($vendor);
        });

        $this->output = value($this->outputer);

        Rescue::create($callback)->fail($this->process(...))->call();
    }

    public static function setConfigurator(?Closure $configurator): void
    {
        self::$configurator = Caller::create($configurator)->add(fn (Cli $cli) => function (string $buffer) use ($cli) {
            once(fn () => $cli->error('Unexpected output from event closure'))->call();

            return $cli->success($buffer);
        })->get();
    }

    public static function serialize(mixed $callback): string
    {
        return Base64::encode(Serializer::create($callback)->serialize());
    }

    public static function unserialize(string $callback): mixed
    {
        return Serializer::create(Base64::decode($callback))->unserialize();
    }

    protected function process(Throwable $exception): void
    {
        throw_if($exception instanceof ClosureNotAllowedException, $exception);

        $this->output->terminal()->clear();

        $vendor = $this->vendor();

        $version = Version::create($this->callback->framework(in: $this->caller)->runningInProcessMode(), $vendor);

        if ($version->failed() && $path = $version->mismatched()) {
            $this->output->error("Package version mismatch between current source and found framework in $path");

            return;
        }

        ProcessFactory::file(collect([$this->vendor()->path(), self::STUB])->toRootDirectory())->stub([
            '__AUTOLOAD__' => $vendor->root()->autoload(),
            '__CALLBACK__' => self::serialize($this->callback),
            '__BUFFER__' => self::serialize($this->outputer)
        ])->tty()->start()->wait();
    }
}
