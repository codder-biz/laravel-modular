<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// Get Files List
if (!function_exists('module_asset')) {
    function module_asset(string $dir)
    {
        $data = explode('::', $dir);
        $module = $data[0];
        $dir = $data[1];
        return asset("storage/modules/$module/$dir");
    }
}

// Get Files List
if (!function_exists('getFiles')) {
    function getFiles(string $dir)
    {
        if (!file_exists($dir)) return [];
        return File::files($dir);
    }
}

// Module Path
if (!function_exists('module_path')) {
    function module_path(?string $module = null, ?string $extra = null)
    {
        if (!$module) return base_path('modules');
        if ($extra) $extra = implode(DIRECTORY_SEPARATOR, explode('/', $extra));

        // Sub-Module
        if (strpos($module, '/') !== false) {
            $module = explode('/', $module);
            $moduleName = array_shift($module);

            $extra = trim(implode(DIRECTORY_SEPARATOR, $module) . DIRECTORY_SEPARATOR . $extra, '/');
            $module = $moduleName;
            unset($moduleName);
        }

        return base_path('modules') . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . $extra;
    }
}

// Get Directories List of a module
if (!function_exists('getModuleDirectories')) {
    function getModuleDirectories(string $module)
    {
        $dir = module_path($module);
        if (!File::exists($dir)) return [];

        $mainModulesPath = module_path();
        return array_map(function ($m) use ($mainModulesPath) {
            return str_replace($mainModulesPath . '/', '', $m);
        }, File::directories($dir));
    }
}

// Modules List
if (!function_exists('modules')) {
    /**
     * Get all modules names.
     *
     * @return array
     */
    function modules()
    {
        if (!file_exists($dir = module_path())) return [];

        $list = [];
        foreach (File::directories($dir) as $module) {
            $module = str_replace($dir . DIRECTORY_SEPARATOR, '', $module);
            $list[] = $module;
        }

        return $list;
    }
}

// Config Path
if (!function_exists('module_config_path')) {
    function module_config_path(string $module, ?string $extra = null)
    {
        return module_path($module, 'Config' . ($extra ? '/' . $extra : null));
    }
}

// Database Path
if (!function_exists('module_database_path')) {
    function module_database_path(string $module, ?string $extra = null)
    {
        return module_path($module, 'Database' . ($extra ? '/' . $extra : null));
    }
}

// Resources Path
if (!function_exists('module_resource_path')) {
    function module_resource_path(string $module, ?string $extra = null)
    {
        return module_path($module, 'Resources' . ($extra ? '/' . $extra : null));
    }
}

// Convert studly to slug
if (!function_exists('studlyToSlug')) {
    function studlyToSlug(string $text)
    {
        return Str::slug(strtolower(trim(preg_replace('/[A-Z-]/', ' $0', $text))));
    }
}
