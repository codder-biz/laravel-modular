# Laravel Modular

<a href="https://packagist.org/packages/codder/laravel-modular"><img src="https://img.shields.io/packagist/v/codder/laravel-modular.svg?style=flat-square" alt="Total Downloads">
</a>
<a href="https://packagist.org/packages/codder/laravel-modular"><img src="https://img.shields.io/packagist/dt/codder/laravel-modular.svg?style=flat-square" alt="Latest Stable Version">
</a>
<a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

`codder/laravel-modular` is a module system for Laravel applications. It uses
[Composer path repositories](https://getcomposer.org/doc/05-repositories.md#path) for autoloading, and [Laravel package discovery](https://laravel.com/docs/7.x/packages#package-discovery) for module initialization, and then provides minimal tooling to fill in any gaps. These modules use the existing 
[Laravel package system](https://laravel.com/docs/7.x/packages), and follow existing Laravel
conventions.

## Documentation
The documentation will be available in soon.

## Installation

You can install the package via composer:

```bash
composer require codder/laravel-modular
```

### Create a module

Next, let's create a module:

```shell script
php artisan module:make foo 
```

Modular will scaffold up a new module for you:

```
modules/
  composer.json
  foo/
    app/
    config/
    database/
    public/
    resources/
    routes/
```

### Assets
Your assets are stored in ```modules/MODULE/public``` after that run ```php artisan storage:link``` to create symbolic links from your assets to public folder.

To call the assets in your blade just call the helper ```module_asset('foo::bar.jpg')```

### Livewire
This package supports Livewire >= 2!

## License

This package is an open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
