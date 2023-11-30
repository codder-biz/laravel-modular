<?php

namespace Codder\Laravel\Modular\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->routes(function () {
            foreach (modules() as $module) {
                $this->mapApiRoutes($module);
                $this->mapWebRoutes($module);
            }
        });
    }

    private function mapApiRoutes(string $module): void
    {
        $routes = Route::prefix('api')
            ->middleware(['api', 'throttle:60,1'])
            ->name($module . '.');

        if ($this->app->runningInConsole() && in_array('module:routes', $_SERVER['argv'] ?? [])) {
            $routes->name($module . '.');
        }

        $routes->group(module_path($module, 'routes/api.php'));
    }

    private function mapWebRoutes(string $module): void
    {
        $routes = Route::middleware('web')
            ->middleware('throttle:60,1')
            ->name($module . '.');

        if ($this->app->runningInConsole() && in_array('module:routes', $_SERVER['argv'] ?? [])) {
            $routes->name($module . '.');
        }

        $routes->group(module_path($module, 'routes/web.php'));
    }
}
