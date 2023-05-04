<?php

namespace Mpietrucha\Events;

use Closure;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\StorageInterface;

class Result
{
    use HasFactory;

    protected array $events = [];

    protected Collection $contexts;

    public function __construct(protected Collection $result, protected string $event, array $contexts, protected string $caller)
    {
        $this->contexts = collect($contexts);
    }

    public function event(): string
    {
        return $this->event;
    }

    public function events(?StorageInterface $storage = null, ?Closure $handler = null): ?Collection
    {
        $events = collect($this->events);

        if ($storage && $events->splice(1, 0, [$handler])) {
            $events->filter()->each(fn (Closure $event) => $event($storage, $this));
        }

        return $events->withoutMiddle();
    }

    public function withEvents(Closure $before, Closure $after): self
    {
        $this->events = [$before, $after];

        return $this;
    }

    public function before(): ?Closure
    {
        return $this->events()?->first();
    }

    public function after(): ?Closure
    {
        return $this->events()?->last();
    }

    public function get(): Collection
    {
        return $this->result->recursive()->map($this->map(...))->filter()->flatten()->chunk(2);
    }

    protected function map(Collection $result): ?array
    {
        [$callback, $contexts] = $result;

        if (! $callback instanceof Closure) {
            return null;
        }

        if (! $contexts->count() && ! $this->contexts->count()) {
            return [$callback, $this->caller];
        }

        return $this->context(
            $contexts,
            $this->contexts->whenEmpty(fn (Collection $contexts) => $contexts->push($this->caller))
        )->map(fn () => [$callback, $this->caller])->toArray();
    }

    protected function context(Collection $source, Collection $compare): Collection
    {
        return $source->map(function (string $context) use ($compare) {
            return $compare->toStringable()->filter->is($context)->count();
        })->filter();
    }
}
