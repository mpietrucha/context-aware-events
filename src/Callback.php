<?php

namespace Mpietrucha\Events;

use Closure;
use Exception;
use Mpietrucha\Support\Condition;
use Mpietrucha\Support\Reflector;
use Mpietrucha\Support\Bootstrapper;
use Mpietrucha\Support\SerializableClosure;
use Mpietrucha\Finder\FrameworksFinder;
use Illuminate\Support\Traits\Tappable;
use Mpietrucha\Events\Exception\ProcessNotAllowedException;
use Mpietrucha\Events\Exception\ClosureNotAllowedException;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;
use Mpietrucha\Support\Concerns\HasFactoryWithSerializableCallbacks;

class Callback
{
    use Tappable;

    use HasFactoryWithSerializableCallbacks;

    protected int $counter = 0;

    protected bool $process = false;

    protected ?SerializableClosure $bootstrap = null;

    protected static ?Bootstrapper $bootstrapper = null;

    protected const FRAMEWORK_FINDER_CACHE_KEY = 'context.aware.storage.framework';

    public function __construct(protected ?SerializableClosure $callback = null)
    {
    }

    public function __invoke(): void
    {
        if (! $callback = $this->callback) {
            return;
        }

        $this->counter++;

        if (self::$bootstrapper) {
            $this->assertProcessContext();
        }

        $this->bootstrap?->invoke();

        $callback();
    }

    public static function setBootstrapper(?Bootstrapper $bootstrapper): void
    {
        self::$bootstrapper = $bootstrapper;
    }

    public function bind(self $instance = new self): ?Closure
    {
        return $this->callback?->bindTo($instance, $instance);
    }

    public function unbind(): ?self
    {
        if (! $this->callback) {
            return null;
        }

        $instance = Reflector::closure($this->callback)->getClosureThis();

        if (! $instance) {
            return $this->tap($this->bind(...));
        }

        return $instance->tap(fn (self $instance) => $instance->callback = $this->callback);
    }

    public function runningInProcessMode(): self
    {
        $this->process = true;

        if ($bootstrapper = self::$bootstrapper) {
            $this->bootstrap = SerializableClosure::create(fn () => $bootstrapper->bootstrap());
        }

        return $this;
    }

    public function bootstrap(string $path, ?Closure $callback = null, bool $vendor = false): self
    {
        if (! $this->shouldHandleNewBootstrapper()) {
            return $this;
        }

        self::setBootstrapper(
            Bootstrapper::create($path, $callback)->vendor($vendor)
        );

        return $this;
    }

    public function framework(?string $name = null, ?string $in = null): self
    {
        if (! $this->shouldHandleNewBootstrapper()) {
            return $this;
        }

        $bootstrapper = FrameworksFinder::create()->in($in)->cache([
            self::FRAMEWORK_FINDER_CACHE_KEY, $name, $in
        ])->instance(function (FrameworkFinderInterface $framework) use ($name) {
            if (! $name) {
                return true;
            }

            return $framework->name() === $name;
        })->instances()->first()?->bootstrapper();

        self::setBootstrapper($bootstrapper);

        return $this;
    }

    protected function shouldHandleNewBootstrapper(): bool
    {
        if ($this->process) {
            return true;
        }

        return self::$bootstrapper === null;
    }

    protected function assertProcessContext(): void
    {
        throw_unless(
            $this->callback,
            new ClosureNotAllowedException('Running outside callback is not allowed in this instance')
        );

        throw_unless(
            $this->process,
            new ClosureNotAllowedException('Running outside process is not allowed in this instance')
        );
    }

    protected function assertClosureContext(): void
    {
        throw_if(
            $this->process,
            new ProcessNotAllowedException('Running inside process is not allowed in this instance')
        );
    }
}
