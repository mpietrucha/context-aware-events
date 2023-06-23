<?php

require_once '__AUTOLOAD__';

\Mpietrucha\Events\Process::unserialize('__CALLBACK__')->bootstrapped(function () {
    \Mpietrucha\Events\Process::unserialize('__BUFFER__')();
})();
