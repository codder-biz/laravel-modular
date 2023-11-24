<?php

namespace Codder\Laravel\Modular;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{Artisan, Event};
use Illuminate\Console\Events\CommandFinished;
use Codder\Laravel\Modular\ModulesFinder;

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

        // If storage:link finish so call our storage link.
        Event::listen(function (CommandFinished $event) {
            if ($event->command == 'storage:link') {
                Artisan::call('module:storage:link');
                dump(Artisan::output());
            }
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Console
        if ($this->app->runningInConsole())
            $this->console();
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
            $this->app->register($provider);
        }
    }
}
