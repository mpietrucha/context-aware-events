<?php

namespace Mpietrucha\Events;

use Closure;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Support\Concerns\HasFactory;

class Context
{
    use HasFactory;

    protected array $contexts = [];

    protected ?Result $result = null;

    public function __construct(protected Closure $handler)
    {
    }

    public function __destruct()
    {
        $this->result();
    }

    public function result(): Result
    {
        return $this->result ??= ($this->handler)($this->contexts);
    }

    public function context(string|array $contexts): self
    {
        $this->contexts = Arr::wrap($contexts);

        return $this;
    }

    public function app(): self
    {
        return $this->context(collect([
            Vendor::create()->root()->path(), '*'
        ])->toDirectory());
    }
}
