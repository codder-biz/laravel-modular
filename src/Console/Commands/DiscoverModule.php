<?php

namespace Codder\Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;

class DiscoverModule extends Command
{
    protected $signature = 'module:discover';

    protected $description = 'Discover modules to composer require autoloads';

    public function handle()
    {
        $this->line('Please wait, we are discovering your modules!');
        exec('cd ' . base_path() . ' && composer update modules/modules > /dev/null 2>&1');
    }
}
