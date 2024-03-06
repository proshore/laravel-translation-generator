<?php

namespace Proshore\Translator;

use Illuminate\Support\ServiceProvider;
use Proshore\Translator\Commands\TranslateLangFiles;

class TranslationServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->registerConfigs();

        $this->app->bind(TranslatorClient::class, function () {
            return TranslatorClient::make();
        });
    }

    public function boot(): void
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                TranslateLangFiles::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/translator.php' => config_path('translator.php'),
        ], 'translator');
    }

    private function registerConfigs(): void
    {
        $configPath = __DIR__ . '/../config/translator.php';
        $this->mergeConfigFrom($configPath, 'translator');
    }

}
