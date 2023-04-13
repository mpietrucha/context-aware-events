<?php

namespace Mpietrucha\Events;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;

class Result
{
    use HasFactory;

    protected Collection $contexts;

    protected static ?int $contextCompareMode = null;

    public const CONTEXT_COMPARE_BOTH = 0;

    public const CONTEXT_COMPARE_SOURCE_CLOSURE = 1;

    public const CONTEXT_COMPARE_SOURCE_DISPATCHER = 2;

    public function __construct(protected Collection $result, protected string $event, array $contexts, protected string $caller)
    {
        $this->contexts = collect($contexts);
    }

    public static function setContextCompareMode(?int $contextCompareMode): void
    {
        self::$contextCompareMode = $contextCompareMode ?? self::CONTEXT_COMPARE_SOURCE_CLOSURE;
    }

    public function event(): string
    {
        return $this->event;
    }

    public function get(): Collection
    {
        return $this->result->filter($this->filter(...))->map->first();
    }

    protected function filter(Collection $result): bool
    {
        [, $contexts] = $result;

        if (! $contexts->count() && ! $this->contexts->count()) {
            return true;
        }

        $contextsWithCaller = $this->contexts->whenEmpty(fn (Collection $contexts) => $contexts->push($this->caller));

        [$compareSourceClosure, $compareSourceDispatcher] = [
            $this->context($contexts, $contextsWithCaller),
            $this->context($this->contexts, $contexts)
        ];

        if (self::$contextCompareMode === self::CONTEXT_COMPARE_SOURCE_CLOSURE) {
            return $compareSourceClosure;
        }

        if (self::$contextCompareMode === self::CONTEXT_COMPARE_SOURCE_DISPATCHER) {
            return $compareSourceDispatcher;
        }

        return $compareSourceClosure || $compareSourceDispatcher;
    }

    protected function context(Collection $source, Collection $compare): bool
    {
        return $source->filter(function (string $context) use ($compare) {
            return $compare->toStringable()->filter->is($context)->count();
        })->count();
    }
}
