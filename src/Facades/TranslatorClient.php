<?php

namespace Proshore\Translator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static translate()
 * @method static translateBatch()
 * @method static languages()
 */
class TranslatorClient extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return  \Proshore\Translator\TranslatorClient::class;
    }
}
