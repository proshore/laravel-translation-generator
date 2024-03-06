<?php

it("cannot_translated_without_keys", function () {
    $translator = \Proshore\Translator\TranslatorClient::make();
    $translator->translate("Hello Worlds", "np");
})->throws(Exception::class);
