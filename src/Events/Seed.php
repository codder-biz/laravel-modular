<?php

namespace Codder\Laravel\Modular\Events;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;

class Seed
{
    public function handle(CommandFinished $event): void
    {
        if ($event->command == 'db:seed') {
            foreach (modules() as $module) {
                $module = ucfirst($module);
                $class = "Modules\\$module\\Database\\Seeders\\DatabaseSeeder";
                if (class_exists($class)) (new $class)->run();
            }
        }
    }
}
