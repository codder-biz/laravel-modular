<?php

namespace Codder\Laravel\Modular\Providers;

use Illuminate\Support\ServiceProvider;

class BroadcastModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach (modules() as $module) {
            $dir = module_path($module, 'Routes/channels.php');
            if (file_exists($dir)) require $dir;
        }
    }
}
