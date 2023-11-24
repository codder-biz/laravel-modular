<?php

namespace Codder\Laravel\Modular\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    private static string $module;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        foreach (modules() as $module) {
            // The controller namespace for the application.
            // When present, controller route declarations will automatically be prefixed with this namespace.
            $this->namespace = "Modules\\$module\\Http\\Controllers";
            $this::$module = $module;
            $isConsole = $this->app->runningInConsole();
            $module = studlyToSlug($this::$module);

            $this->routes(function () use ($isConsole, $module) {
                $this->mapApiRoutes($isConsole, $module);
                $this->mapWebRoutes($isConsole, $module);
            });
        }
    }

    /**
     * Load API routes
     *
     * @param boolean $isConsole
     * @param string $module
     * @return void
     */
    private function mapApiRoutes(bool $isConsole, string $module)
    {
        $routes = Route::middleware('api')
            ->namespace($this->namespace)
            ->prefix('api')
            ->name($module . '.');

        if ($isConsole && in_array('module:routes', $_SERVER['argv'] ?? [])) {
            $routes->name($module . '.');
        }

        $routes->group(module_path($this::$module, 'Routes/api.php'));
    }

    /**
     * Load Web routes
     *
     * @param boolean $isConsole
     * @param string $module
     * @return void
     */
    private function mapWebRoutes(bool $isConsole, string $module)
    {
        $routes = Route::middleware('web')
            ->namespace($this->namespace)
            ->name($module . '.');

        if ($isConsole && in_array('module:routes', $_SERVER['argv'] ?? [])) {
            $routes->name($module . '.');
        }

        $routes->group(module_path($this::$module, 'Routes/web.php'));
    }
}
