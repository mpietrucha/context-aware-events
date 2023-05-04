<?php

namespace Mpietrucha\Events\Configurator;

use Closure;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Events\Contracts\OutputConfiguratorInterface;

class OutputConfigurator implements OutputConfiguratorInterface
{
    use HasFactory;

    public function __construct(protected bool $disable = false, protected bool $warning = true, protected string $type = 'warning')
    {
        $this->configure();
    }

    public function configure(): void
    {
    }

    public function withDisable(Closure|bool $mode): self
    {
        $this->disable = value($mode);

        return $this;
    }

    public function withWarning(Closure|bool $mode): self
    {
        $this->warning = value($mode);

        return $this;
    }

    public function withType(Closure|string $type): self
    {
        $this->type = value($type);

        return $this;
    }

    public function disable(): bool
    {
        return $this->disable;
    }

    public function warning(): bool
    {
        return $this->warning;
    }

    public function type(): string
    {
        return $this->type;
    }
}
