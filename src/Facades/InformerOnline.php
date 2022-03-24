<?php

namespace RLWebdiensten\LaravelInformerOnline\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \RLWebdiensten\LaravelInformerOnline\InformerOnline
 */
class InformerOnline extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-informeronline';
    }
}
