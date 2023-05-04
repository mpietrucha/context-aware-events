<?php

namespace Mpietrucha\Events\Storage;

use Closure;
use Mpietrucha\Events\Result;
use Mpietrucha\Storage\Adapter;
use Mpietrucha\Support\Reflector;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasVendor;
use Mpietrucha\Events\Contracts\StorageInterface;

class TemporaryFilesystemStorage implements StorageInterface
{
    use HasVendor;

    protected Adapter $storage;

    public function __construct()
    {
        $this->storage = Adapter::create()->table($this->vendor());
    }

    public function add(string $event, array $contexts, string $caller, Closure $callback): Result
    {
        $this->storage->appendUnique($event, [$callback, $contexts], function (array $entry, array $added) {
            [$entryCallback, $entryContexts, $addedCallback, $addedContexts] = [...$entry, ...$added];

            if ($entryContexts !== $addedContexts) {
                return false;
            }

            return (string) Reflector::closure($entryCallback) === (string) Reflector::closure($addedCallback);
        });

        return $this->get($event, $contexts, $caller);
    }

    public function get(string $event, array $contexts, string $caller): Result
    {
        return Result::create(
            $this->storage->get($event) ?? collect(),
            $event, $contexts, $caller
        );
    }

    public function delete(string $event): void
    {
        $this->storage->forget($event);
    }

    public function truncate(): void
    {
        $this->storage->delete();
    }
}
