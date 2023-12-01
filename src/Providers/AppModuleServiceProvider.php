<?php

namespace Codder\Laravel\Modular\Providers;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class AppModuleServiceProvider extends IlluminateServiceProvider
{
    /**
     * Module's configurations
     *
     * @var string
     */
    public static $module = null;

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        foreach (modules() as $module) {
            $this::$module = $module;

            // Modulator Loaders
            $this->loadHelpers();
            $this->loadConfig();
            $this->loadViews();
            $this->loadMigrations();
            $this->registerCommands();
            $this->loadTranslations();
        }
    }

    /**
     * Load Module Helpers.
     *
     * @return void
     */
    protected function loadHelpers()
    {
        if (!static::$module) return;

        if (file_exists($helpers = module_path(static::$module, 'helpers.php'))) {
            require $helpers;
        }
    }

    /**
     * Load Module Configrations.
     *
     * @return void
     */
    protected function loadConfig()
    {
        if (!static::$module) return;

        if (file_exists($config = module_config_path(static::$module))) {
            foreach (getFiles($config) as $file) {
                $this->mergeConfigFrom($file->getPathname(), static::$module);
            }
        }
    }

    /**
     * Register all the commands of a single module.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if (!static::$module) return;

        if ($this->app->runningInConsole()) {
            if (file_exists($commands = module_path(static::$module, 'Commands'))) {
                $module = 'Modules\\' . str_replace('/', '\\', static::$module) . '\\App\\Console\\Commands\\';
                $commands = array_map(function ($command) use ($module) {
                    return $module . str_replace('.php', '', $command->getFileName());
                }, getFiles($commands));

                $this->commands($commands);
            }

            $console = module_path(static::$module, 'routes/console.php');
            if (file_exists($console)) require $console;
        }
    }

    /**
     * Load all translations of a single module.
     *
     * @return void
     */
    protected function loadTranslations()
    {
        if (file_exists($dir = module_path(static::$module, 'resources/lang')))
            $this->loadTranslationsFrom($dir, studlyToSlug(static::$module));
    }

    /**
     * Load all migrations of a single module.
     *
     * @return void
     */
    protected function loadMigrations()
    {
        if (file_exists($dir = module_path(static::$module, 'ratabase/migrations')))
            $this->loadMigrationsFrom($dir);
    }

    /**
     * Load all views of a single module.
     *
     * @return void
     */
    protected function loadViews()
    {
        if (file_exists($dir = module_path(static::$module, 'resources/views')))
            $this->loadViewsFrom($dir, studlyToSlug(static::$module));
    }
}
