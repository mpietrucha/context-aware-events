<?php

require_once '__AUTOLOAD__';

$serialized = \Mpietrucha\Support\Base64::decode('__CALLBACK__');

$callback = \Mpietrucha\Support\Serializer::create($serialized)->unserialize();

$callback->bootstrapped(function () {
    \Mpietrucha\Error\Handler::create(\Mpietrucha\Error\Handler\DefaultHandler::class)->register();

    \Mpietrucha\Cli\Buffer::createWithNewLine(function () {
        $this->encryptable()->handlers()->get(\Mpietrucha\Cli\Buffer\Handlers\SymfonyVarDumperHandler::class)->supportsColors(__COLOR__);
    });
});

$callback();
