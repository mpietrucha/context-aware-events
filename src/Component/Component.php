<?php

namespace Mpietrucha\Events\Component;

use Closure;
use Mpietrucha\Events\Result;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Events\Context;
use Mpietrucha\Events\Bootstrap;
use Mpietrucha\Events\Callback;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\StorageInterface;
use Mpietrucha\Events\Contracts\ComponentInterface;

abstract class Component implements ComponentInterface
{
    use HasFactory;

    protected ?Closure $callback = null;

    public function __construct(protected string $event, mixed $callback = null)
    {
        $this->callback = $callback instanceof Closure ? $callback : null;
    }

    public static function __callStatic(string $method, array $arguments): Context
    {
        return self::create($method, ...$arguments)->context();
    }

    public function handle(StorageInterface $storage, Result $result): void
    {
        $result->events($storage);
    }

    public function context(): Context
    {
        return Context::create($this->result(...));
    }

    public function result(array $contexts = []): Result
    {
        $storage = Bootstrap::create();

        $result = $storage->{$this->getStorageAccessor()}(
            $this->event,
            $contexts,
            Vendor::create()->path(),
            Callback::create($this->callback)->bind()
        )->withEvents(...$this->getGlobalEvents());

        collect([
            Bootstrap::before(...), $this->handle(...), Bootstrap::after(...)
        ])->each(fn (Closure $event) => $event($storage, $result));

        return $result;
    }
}
