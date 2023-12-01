<?php

namespace Codder\Laravel\Modular\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Livewire\Livewire;

class MakeModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Laravel module';

    private array $modules;

    private string $module;

    private bool $isFirst;

    private bool $hasLivewire;

    private string $dir;

    private Filesystem $filesystem;

    public function __construct()
    {
        parent::__construct();
        $this->filesystem = new Filesystem;
        $this->modules = modules();
        $this->hasLivewire = class_exists(Livewire::class);
        $this->isFirst = count($this->modules) !== 1;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->module = strtolower($this->argument('module'));

        if (!File::exists($this->dir = base_path("modules/$this->module"))) {
            File::deleteDirectory($this->dir);
            $this->makeDirectoriesWithGitKeep($this->dir);
            $this->writeStubs();
            $this->info('Your module has been created!');
            $this->line('Please wait, we are discovering your modules!');
            $this->composerModule();
            $this->composerApp();
            exec('cd ' . base_path() . ' && composer update modules/modules > /dev/null 2>&1');
            if ($this->hasLivewire) $this->line('Livewire, try to access: <info>' . url('counter') . '</info>');
            return $this->line('Finished, try to access: <info>' . url($this->module) . '</info>');
        }

        return $this->error('This module already exists!');
    }

    private function makeDirectoriesWithGitKeep()
    {
        if (!File::exists('modules')) {
            File::makeDirectory('modules');
        }

        File::makeDirectory("modules/{$this->module}");

        foreach ([
            "config" => false,
            "routes" => false,
            "resources" => false,
            "resources/views" => false,
            "public" => true,
            "database" => false,
            "database/factories" => true,
            "database/migrations" => true,
            "database/seeders" => true,
            "app" => false,
            "app/Http" => false,
            "app/Http/Controllers" => false,
            "app/Http/Livewire" => !$this->hasLivewire,
            "app/Models" => true,
            "app/Providers" => true
        ] as $dir => $keep) {
            File::makeDirectory("{$this->dir}/$dir");
            if ($keep) touch("{$this->dir}/$dir/.gitkeep");
        }
    }

    private function stubPath($filename): string
    {
        return str_replace('\\', '/', dirname(__DIR__, 3)) . "/stubs/$filename";
    }

    protected function getStubs(): array
    {
        $data = [
            'composer.stub' => base_path('modules/composer.json'),
            'config.stub' => "config/$this->module.php",
            'DatabaseSeeder.stub' => 'database/seeders/DatabaseSeeder.php',
            'ExampleController.stub' => 'app/Http/Controllers/ExampleController.php',
            'view.stub' => 'resources/views/example.blade.php',
            'web.stub' => 'routes/web.php',
            'api.stub' => 'routes/api.php',
        ];

        if ($this->hasLivewire) {
            $data['Counter.stub'] = 'app/Http/Livewire/Counter.php';
            $data['view_livewire.stub'] = 'resources/views/livewire/counter.blade.php';
        }

        return $data;
    }

    private function writeStubs(): void
    {
        $placeholders = [
            '{{ namespace }}' => ucfirst($this->module),
            '{{ module }}' => $this->module,
        ];

        $search = array_keys($placeholders);
        $replace = array_values($placeholders);

        foreach ($this->getStubs() as $stub_file => $destination) {
            $filename = $this->isFirst && $stub_file === 'composer.stub'
                ? $destination
                : "{$this->dir}/{$destination}";

            $contents = file_get_contents($this->stubPath($stub_file));
            $destination = str_replace($search, $replace, $destination);
            $output = str_replace($search, $replace, $contents);

            $this->filesystem->ensureDirectoryExists($this->filesystem->dirname($filename));
            $this->filesystem->put($filename, $output);
        }

        if ($this->hasLivewire) {
            $this->filesystem->append("{$this->dir}/routes/web.php", "Route::get('/counter', 'Modules\\".ucfirst($this->module)."\\App\Http\Livewire\Counter');");
        }
    }

    private function composerModule()
    {
        $ucfirst = ucfirst($this->module);
        $dir = base_path('modules/composer.json');
        $json = json_decode(file_get_contents($dir), true);
        $json['autoload']['psr-4']["Modules\\{$ucfirst}\\App\\"] = "{$this->module}/app/";
        $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->filesystem->put($dir, $json);
    }

    private function composerApp()
    {
        if ($this->isFirst) {
            $dir = base_path('composer.json');
            $json = json_decode(file_get_contents($dir), true);
            $json['require']['modules/modules'] = '*';
            $json['repositories'][] = ['type' => 'path', 'url' => 'modules', 'options' => ['symlink' => true]];
            $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $this->filesystem->put($dir, $json);
        }
    }
}
