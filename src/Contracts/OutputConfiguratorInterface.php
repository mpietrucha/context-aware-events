<?php

namespace Mpietrucha\Events\Contracts;

interface OutputConfiguratorInterface
{
    public function disable(): bool;

    public function warning(): bool;

    public function type(): string;
}
