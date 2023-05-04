<?php

require_once '__AUTOLOAD__';

\Mpietrucha\Cli\Buffer::newLine();

$serialized = \Mpietrucha\Support\Base64::decode('__CALLBACK__');

\Mpietrucha\Support\Serializer::create($serialized)->unserialize()();
