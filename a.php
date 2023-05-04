<?php

require 'vendor/autoload.php';

use Mpietrucha\Events\Component\Event;
use Mpietrucha\Events\Component\Dispatcher;
use Mpietrucha\Events\Bootstrap;
use Illuminate\Support\Facades\Artisan;
use Mpietrucha\Support\Bootstrapper;
use Mpietrucha\Finder\FrameworksFinder;

$bootstrapper = FrameworksFinder::create()->in('/Users/michalpietrucha/Documents/webs/offsite')->cache('xd')->instances()->first()?->bootstrapper();

$bootstrapper->bootstrap();

Artisan::call('inspire');

// Bootstrap::closuresOutputConfigurator()->withDisable(false)->withWarning(false)->withType('success');

// Bootstrap::create()->truncate();

// Event::composer(function () {
//     $this->assertProcessContext();
//
//     Artisan::call('inspire');
// });

// Dispatcher::composer();
