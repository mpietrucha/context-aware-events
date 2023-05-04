<?php

namespace Mpietrucha\Events;

use Psr\Log\LoggerInterface;
use Mpietrucha\Support\Bootstrapper;
use Mpietrucha\Events\Concerns\GlobalEvents;
use Mpietrucha\Events\Contracts\StorageInterface;
use Mpietrucha\Events\Storage\TemporaryFilesystemStorage;
use Mpietrucha\Events\Contracts\OutputConfiguratorInterface;
use Mpietrucha\Events\Configurator\OutputConfigurator;

class Bootstrap
{
    use GlobalEvents;

    protected static ?LoggerInterface $logger = null;

    protected static ?StorageInterface $storage = null;

    protected static ?Bootstrapper $bootstrapper = null;

    protected static ?OutputConfiguratorInterface $closuresOutputConfigurator = null;

    public static function create(): StorageInterface
    {
        return self::$storage ??= new TemporaryFilesystemStorage;
    }

    public static function logger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

    public static function storage(StorageInterface $storage): void
    {
        self::$storage = $storage;
    }

    public static function bootstrapper(Bootstrapper $bootstrapper): void
    {
        self::$bootstrapper = $bootstrapper;
    }

    public static function closuresOutputConfigurator(null|Closure|OutputConfiguratorInterface $configurator = null): OutputConfiguratorInterface
    {
        self::$closuresOutputConfigurator ??= OutputConfigurator::create();

        self::$closuresOutputConfigurator = value($configurator, self::$closuresOutputConfigurator) ?? self::$closuresOutputConfigurator;

        return self::$closuresOutputConfigurator;
    }
}
