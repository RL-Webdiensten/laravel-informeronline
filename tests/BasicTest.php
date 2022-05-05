<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use RLWebdiensten\LaravelInformerOnline\Exceptions\InvalidResponseException;
use RLWebdiensten\LaravelInformerOnline\InformerOnline;

it('registers facade', function () {
    expect(app()->make('RLWebdiensten\LaravelInformerOnline\InformerOnline'))->toBeInstanceOf(InformerOnline::class);
    expect(app(InformerOnline::class))->toBeInstanceOf(InformerOnline::class);
    expect(app('laravel-informeronline'))->toBeInstanceOf(InformerOnline::class);
});

it('can authenticate', function () {
    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('request')
        ->andReturn(new Response(200, [], '{
                "administration": {
                    "company_name": "Test",
                    "contact": "",
                    "street": "",
                    "house_number": "",
                    "house_number_suffix": "",
                    "zip": "",
                    "city": "",
                    "country": "NL",
                    "phone": "",
                    "mobile": "",
                    "email": "test@example.com",
                    "coc": "",
                    "vat": "",
                    "iban": "",
                    "trade_module": "disabled"
                }
            }'));

    app()->instance(Client::class, $guzzleMock);
    app()->bind(InformerOnline::class);

    Config::set('informeronline.api_key', 'TEST');
    Config::set('informeronline.security_code', 'TEST');

    expect(\RLWebdiensten\LaravelInformerOnline\Facades\InformerOnline::getAdministrationDetails())->toBeArray()->toHaveKey('administration');
});

it('does not authenticate when we get an 400 error', function () {
    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('request')
        ->andReturn(new Response(400, []));

    app()->instance(Client::class, $guzzleMock);
    app()->bind(InformerOnline::class);

    Config::set('informeronline.api_key', 'TEST');
    Config::set('informeronline.security_code', 'TEST');

    \RLWebdiensten\LaravelInformerOnline\Facades\InformerOnline::getAdministrationDetails();
})->throws(InvalidResponseException::class);

it('does not authenticate when we get an empty body', function () {
    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('request')
        ->andReturn(new Response(200, [], ''));

    app()->instance(Client::class, $guzzleMock);
    app()->bind(InformerOnline::class);

    Config::set('informeronline.api_key', 'TEST');
    Config::set('informeronline.security_code', 'TEST');

    \RLWebdiensten\LaravelInformerOnline\Facades\InformerOnline::getAdministrationDetails();
})->throws(InvalidResponseException::class);
