# Laravel Modular

[![Latest Version on Packagist](https://img.shields.io/packagist/v/codder/laravel-modular.svg?style=flat-square)](https://packagist.org/packages/codder/laravel-modular)
[![Total Downloads](https://img.shields.io/packagist/dt/codder/laravel-modular.svg?style=flat-square)](https://packagist.org/packages/codder/laravel-modular)


## Installation

You can install the package via composer:

```bash
composer require codder/laravel-modular
```

### Autoloading

By default, the module classes are not loaded automatically. You can autoload your modules using `psr-4`. For example:

``` json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "modules/",
      "Database\\Factories\\": "database/factories/",
      "Database\\Seeders\\": "database/seeders/"
  }
}
```

## Documentation

The documentation will be available in soon.

Tips: The skeleton follows the same as [nWidart/laravel-modules](https://nwidart.com/laravel-modules/v6/introduction).


## License

This package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
