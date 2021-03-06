<?php

namespace RLWebdiensten\LaravelInformerOnline;

use RLWebdiensten\LaravelInformerOnline\Contracts\InformerOnlineConfig as Config;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class InformerOnlineServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-informeronline')
            ->hasConfigFile();

        $this->app->alias(InformerOnline::class, 'laravel-informeronline');

        $this->app->singleton(Config::class, function () {
            return new InformerOnlineConfig(strval(config('informeronline.base_uri')), strval(config('informeronline.api_key')), intval(config('informeronline.security_code')));
        });
    }
}
