<?php

namespace Georgie\Module;

use Georgie\Module\Commands\BuildCreateCommand;
use Georgie\Module\Commands\ModelCreateCommand;
use Georgie\Module\Commands\PermissionCreateCommand;
use Georgie\Module\Services\MenusService;
use Illuminate\Support\ServiceProvider;
use Georgie\Module\Commands\ModuleCreateCommand;
use Georgie\Module\Commands\ConfigCreateCommand;

class LaravelServiceProvider extends ServiceProvider
{
    public $singletons = [
        'g-menu' => MenusService::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ModuleCreateCommand::class,
                ConfigCreateCommand::class,
                PermissionCreateCommand::class,
                ModelCreateCommand::class,
                BuildCreateCommand::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        //配置文件
        $this->publishes([
            __DIR__.'/georgie_config.php' => config_path('georgie_config.php'),
            __DIR__.'/BaseController.php' => app_path('Http/Controllers/BaseController.php'),
            __DIR__.'/AcceptHeader.php' => app_path('Http/Middleware/AcceptHeader.php'),
            __DIR__.'/AuthMiddleware.php' => app_path('Http/Middleware/AuthMiddleware.php'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('GModule', function () {
            return new Provider();
        });
    }
}
