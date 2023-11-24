<?php

namespace Codder\Laravel\Modular\Events;

use Codder\Laravel\Modular\ModulesFinder;
use Illuminate\Console\Events\CommandFinished;

class Optimize
{
    public function handle(CommandFinished $event): void
    {
        if ($event->command == 'optimize') {
            (new ModulesFinder)->build();
        }
    }
}
