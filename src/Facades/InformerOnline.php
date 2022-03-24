<?php

namespace RLWebdiensten\LaravelInformeronline\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RLWebdiensten\LaravelInformeronline\InformerOnline
 */
class InformerOnline extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-informeronline';
    }
}
