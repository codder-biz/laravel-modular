<?php

namespace Codder\Laravel\Modular\Events;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;

class StorageLink
{
    public function handle(CommandFinished $event): void
    {
        if ($event->command == 'storage:link') {
            Artisan::call('module:storage:link');
            dump(Artisan::output());
        }
    }
}
