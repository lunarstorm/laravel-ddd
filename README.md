# A Laravel toolkit for Domain Driven Design patterns

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/lunarstorm/laravel-ddd/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/lunarstorm/laravel-ddd/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/lunarstorm/laravel-ddd.svg?style=flat-square)](https://packagist.org/packages/lunarstorm/laravel-ddd)

Laravel-DDD is a developer toolkit to support domain driven design (DDD) patterns in Laravel applications. More details to come.

## Installation

You can install the package via composer:

```bash
composer require lunarstorm/laravel-ddd
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-ddd-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-ddd-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-ddd-views"
```

## Usage

```php
$laravelDDD = new Lunarstorm\LaravelDDD();
echo $laravelDDD->echoPhrase('Hello, Lunarstorm!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jasper Tey](https://github.com/JasperTey)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
