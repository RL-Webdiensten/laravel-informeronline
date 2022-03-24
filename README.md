# InformerOnline API client wrapper for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rlwebdiensten/laravel-informeronline.svg?style=flat-square)](https://packagist.org/packages/rlwebdiensten/laravel-informeronline)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/RL-Webdiensten/laravel-informeronline/run-tests?label=tests)](https://github.com/RL-Webdiensten/laravel-informeronline/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/RL-Webdiensten/laravel-informeronline/Check%20&%20fix%20styling?label=code%20style)](https://github.com/RL-Webdiensten/laravel-informeronline/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rlwebdiensten/laravel-informeronline.svg?style=flat-square)](https://packagist.org/packages/rlwebdiensten/laravel-informeronline)

A simple InformerOnline API client wrapper for Laravel.

## Installation

You can install the package via composer:

```bash
composer require rlwebdiensten/laravel-informeronline
```

These are the available ENV variables:

```
INFORMER_BASE_URI="" // Not required - default "api.informer.eu"
INFORMER_API_KEY=""    // Required
INFORMER_SECURITY_CODE=""    // Required
```

## Usage

Using dependency injection
```php
function __construct(\RLWebdiensten\LaravelInformeronline\InformerOnline $informerOnlineService)
{
    $this->informerOnlineService = $informerOnlineService;

    // e.g.
    $relations = $this->informerOnlineService->getRelations();
}
```

Using the facade
```php
function someMethod()
{
    $relations = InformerOnline::getRelations();
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Fabian Dingemans](https://github.com/faab007)
- [Rick Lambrechts](https://github.com/ricklambrechts)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
