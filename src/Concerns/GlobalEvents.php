<?php

namespace Mpietrucha\Events\Concerns;

use Mpietrucha\Events\Result;
use Mpietrucha\Events\Callback;
use Mpietrucha\Events\Process;
use Mpietrucha\Events\Contracts\StorageInterface;

trait GlobalEvents
{
    public static function before(StorageInterface $storage, Result $result): void
    {
        self::$logger?->info('mpietrucha/global-events starting');
    }

    public static function beforeEvent(StorageInterface $storage, Result $result): void
    {
        Process::setConfigurator(self::closuresOutputConfigurator());

        self::$logger?->info('mpietrucha/global-events starting event', [
            'event' => $result->event()
        ]);
    }

    public static function beforeDispatch(StorageInterface $storage, Result $result): void
    {
        Callback::setBootstrapper(self::$bootstrapper);

        self::$logger?->info('mpietrucha/global-events starting dispatch', [
            'event' => $result->event()
        ]);
    }

    public static function after(StorageInterface $storage, Result $result): void
    {
        self::$logger?->info('mpietrucha/global-events ending');
    }

    public static function afterEvent(StorageInterface $storage, Result $result): void
    {
        self::$logger?->info('mpietrucha/global-events ending event', [
            'event' => $result->event()
        ]);
    }

    public static function afterDispatch(StorageInterface $storage, Result $result): void
    {
        Callback::setBootstrapper(null);

        self::$logger?->info('mpietrucha/global-events ending dispatch', [
            'event' => $result->event()
        ]);
    }
}
