<?php

require_once '__AUTOLOAD__';

\Mpietrucha\Cli\Buffer::createWithNewLine(function () {
    $handler = $this->handlers()->get(\Mpietrucha\Cli\Buffer\Handlers\SymfonyVarDumperHandler::class);

    $handler->encryptable();
    $handler->supportsColors(__COLORS__);
});

$serialized = \Mpietrucha\Support\Base64::decode('__CALLBACK__');

\Mpietrucha\Support\Serializer::create($serialized)->unserialize()();
