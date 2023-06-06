<?php

require 'vendor/autoload.php';

use Mpietrucha\Events\Bootstrap;
use Mpietrucha\Finder\InstanceFinder;

Bootstrap::closuresOutputConfigurator()->withDisable(false)->withWarning(false)->withType('success');

Bootstrap::create()->truncate();

event_aware('composer', function () {
    $this->assertProcessContext();

    // trigger_error('xd', E_USER_WARNING);
    // echo $this->process ? 'tak' : 'nie';
});

dispatch_aware('composer');
