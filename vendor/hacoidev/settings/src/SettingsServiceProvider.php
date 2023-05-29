<?php

namespace Backpack\Settings;

use Backpack\Settings\app\Models\Setting;
use Config;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Route;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Where the route file lives, both inside the package and in the app (if overwritten).
     *
     * @var string
     */
    public $routeFilePath = '/routes/backpack/settings.php';

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // define the routes for the application
        $this->setupRoutes($this->app->router);

        // only use the Settings package if the Settings table is present in the database
        if (!\App::runningInConsole() && Schema::hasTable(config('backpack.settings.table_name'))) {
            // get all settings from the database
            $settings = Setting::all();

            $config_prefix = config('backpack.settings.config_prefix');

            // bind all settings to the Laravel config, so you can call them like
            // Config::get('settings.contact_email')
            foreach ($settings as $key => $setting) {
                $prefixed_key = !empty($config_prefix) ? $config_prefix . '.' . $setting->key : $setting->key;
                Config::set($prefixed_key, $setting->value);
            }
        }
        // publish the migrations and seeds
        $this->publishes([
            __DIR__ . '/database/migrations/create_settings_table.php.stub' => database_path('migrations/' . config('backpack.settings.migration_name') . '.php'),
        ], 'migrations');

        // publish translation files
        $this->publishes([__DIR__ . '/resources/lang' => app()->langPath() . '/vendor/backpack'], 'lang');

        // publish setting files
        $this->publishes([__DIR__ . '/config' => config_path()], 'config');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'settings');
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__ . $this->routeFilePath;

        // but if there's a file with the same name in routes/backpack, use that one
        if (file_exists(base_path() . $this->routeFilePath)) {
            $routeFilePathInUse = base_path() . $this->routeFilePath;
        }

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // register their aliases
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Setting', \Backpack\Settings\app\Models\Setting::class);

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__ . '/config/backpack/settings.php',
            'backpack.settings'
        );

        foreach (glob(__DIR__ . '/Helper/*.php') as $filename) {
            require_once $filename;
        }
    }
}
