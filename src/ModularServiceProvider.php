<?php

namespace Codder\Laravel\Modular;

use Illuminate\Support\ServiceProvider;
use Codder\Laravel\Modular\ModulesFinder;
use Codder\Laravel\Modular\Providers\EventServiceProvider;

class ModularServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Providers
        $this->app->singleton(ModulesFinder::class);
        $this->registerProviders();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Console
        if ($this->app->runningInConsole()) $this->console();
    }

    /**
     * Publish Config + Register Commands
     *
     * @return void
     */
    public function console()
    {
        // Commands (Modular)
        $this->commands([
            \Codder\Laravel\Modular\Console\Commands\StorageLinkCommand::class,
        ]);
    }

    /**
     * Register providers list
     *
     * @return void
     */
    protected function registerProviders()
    {
        $finder = app(ModulesFinder::class);

        // Cache all current providers
        if (!$finder->cached) {
            $finder->build();
        }

        // Register the exists providers
        foreach ($finder->list as $provider) {
            if (class_exists($provider)) $this->app->register($provider);
        }

        if ($this->app->runningInConsole()) $this->app->register(EventServiceProvider::class);
    }
}
