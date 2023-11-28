<?php

namespace Codder\Laravel\Modular\Providers;

use Codder\Laravel\Modular\Events\{StorageLink, Optimize, OptimizeClear};
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Console\Events\CommandFinished;

class EventModuleServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        CommandFinished::class => [
            StorageLink::class,
            Optimize::class,
            OptimizeClear::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
