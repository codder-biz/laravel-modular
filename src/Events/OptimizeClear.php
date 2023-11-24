<?php

namespace Codder\Laravel\Modular\Events;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\File;

class OptimizeClear
{
    public function handle(CommandFinished $event): void
    {
        if ($event->command == 'optimize:clear') {
            File::delete(base_path('bootstrap/cache/modular-providers.php'));
        }
    }
}
