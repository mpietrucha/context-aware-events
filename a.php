<?php

require 'vendor/autoload.php';

use Mpietrucha\Events\Bootstrap;

Bootstrap::closuresOutputConfigurator()->withDisable(false)->withWarning(false)->withType('success');

Bootstrap::create()->truncate();

event_aware('composer', function () {
    $this->assertProcessContext();

    // trigger_error('xd', E_USER_WARNING);
    // dump('xd');
    echo 'xd';

    dump('nie dzialaja errory i exceptions chyba');
});

dispatch_aware('composer');
