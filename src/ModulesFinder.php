<?php

namespace Codder\Laravel\Modular;

use Illuminate\Support\Facades\File;

class ModulesFinder
{
    private $manifestPath;
    public $cached = false;
    public $list = [];

    function __construct()
    {
        $this->manifestPath = app()->bootstrapPath('cache/modular-providers.php');

        if (File::exists($this->manifestPath)) {
            $this->cached = true;
            $this->list = require($this->manifestPath);
        }
    }

    private function find()
    {
        $this->list = [];
        foreach (['AppModuleServiceProvider', 'BroadcastModuleServiceProvider', 'RouteModuleServiceProvider', 'LivewireModuleServiceProvider'] as $provider) {
            $this->list[] = "Codder\Laravel\Modular\Providers\\$provider";
        }
        $modules = modules();
        if (count($modules) > 0) {
            foreach ($modules as $module) {
                foreach (getFiles($path = module_path($module, 'Providers')) as $provider) {
                    $provider = str_replace($path . '/', '', substr($provider, 0, -4));
                    $this->list[] = 'Modules\\' . str_replace('/', '\\', $module) . '\Providers\\' . $provider;
                }
            }
        }
    }

    private function write()
    {
        if (!is_writable(dirname($this->manifestPath))) {
            throw new \Exception('The ' . dirname($this->manifestPath) . ' directory must be present and writable.');
        }

        File::put($this->manifestPath, '<?php return ' . var_export($this->list, true) . ';', true);
    }

    public function build()
    {
        $this->find();
        $this->write();
    }
}
