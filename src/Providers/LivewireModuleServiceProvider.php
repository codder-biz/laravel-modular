<?php

namespace Codder\Laravel\Modular\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Livewire;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class LivewireModuleServiceProvider extends ServiceProvider
{
    private string $namespace = 'App\\Http\\Livewire';

    public function boot()
    {
        if (class_exists(Livewire::class)) {
            $this->registerModuleComponents();
        }
    }

    public function provides()
    {
        return [];
    }

    protected function registerModuleComponents()
    {
        foreach (modules() as $module) {
            $directory = (string) Str::of(module_path($module))
                ->append('/' . lcfirst($this->namespace))
                ->replace(['\\'], '/');

            $ucfirst = ucfirst($module);
            $namespace = "Modules\\{$ucfirst}\\{$this->namespace}";

            $this->registerComponentDirectory($directory, $namespace, strtolower($module) . '::');
        };
    }

    protected function registerComponentDirectory($directory, $namespace, $aliasPrefix = '')
    {
        $filesystem = new Filesystem();

        if (!$filesystem->isDirectory($directory)) {
            return false;
        }

        collect($filesystem->allFiles($directory))
            ->map(function (SplFileInfo $file) use ($namespace) {
                return (string) Str::of($namespace)
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, Component::class) && !(new ReflectionClass($class))->isAbstract();
            })
            ->each(function ($class) use ($namespace, $aliasPrefix) {
                $alias = $aliasPrefix . Str::of($class)
                    ->after($namespace . '\\')
                    ->replace(['/', '\\'], '.')
                    ->explode('.')
                    ->map([Str::class, 'kebab'])
                    ->implode('.');

                if (Str::endsWith($class, ['\Index', '\index'])) {
                    Livewire::component(Str::beforeLast($alias, '.index'), $class);
                }

                Livewire::component($alias, $class);
            });
    }
}
