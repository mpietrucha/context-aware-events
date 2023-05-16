<?php

use Mpietrucha\Events\Component\Event;
use Mpietrucha\Events\Component\Dispatcher;

if (! function_exists('event_aware')) {
    function event_aware(string $name, Closure $callback): void {
        Event::$name($callback);
    }
}

if (! function_exists('dispatch_aware')) {
    function dispatch_aware(string $name): void {
        Dispatcher::$name();
    }
}
