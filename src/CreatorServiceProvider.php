<?php

namespace Pp\Creator;


use Illuminate\Support\ServiceProvider;
use Pp\Creator\Commands\CreateMenu;
use Pp\Creator\Commands\CreatorInit;

use Pp\Creator\Commands\Generates\Crud\CreateController;
use Pp\Creator\Commands\Generates\Crud\CreateCreator;
use Pp\Creator\Commands\Generates\Crud\CreateCrud;
use Pp\Creator\Commands\Generates\Crud\CreateCrudVue;
use Pp\Creator\Commands\Generates\Crud\CreateForm;
use Pp\Creator\Commands\Generates\Crud\CreateMigration;
use Pp\Creator\Commands\Generates\Crud\CreateModel;
use Pp\Creator\Commands\DbFresh;
use Pp\Creator\Commands\Generates\Crud\CreateAll;

class CreatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../resources/js' => base_path('resources/js'),
            __DIR__.'/../resources/css' => base_path('resources/css'),
            //__DIR__.'/resources/Pages' => base_path('resources/js/Pages'),
            //__DIR__.'/resources/js/Layouts/Authenticated.vue' => base_path('/../resources\js\Layouts\AppLayout.vue'),
            //__DIR__.'/resources/css' => base_path('resources\css'),
            __DIR__.'/../resources/main' => base_path(''),
            __DIR__.'/../storage' => base_path('storage'),
            __DIR__.'/../config/menus.php' => config_path('menus.php'),
            __DIR__.'/../config/creator.php' => config_path('creator.php'),
        ], 'pp-creator');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreatorInit::class,
                CreateMenu::class,
                CreateAll::class,
                CreateController::class,
                CreateCreator::class,
                CreateCrud::class,
                CreateController::class,
                CreateCrudVue::class,
                CreateForm::class,
                CreateMigration::class,
                CreateModel::class,
                DbFresh::class,
            ]);
        }
    }

    // "autoload-dev": {
    //     "psr-4": {
    //         "Tests\\": "tests/",
    //         "Pp\\Trades\\": "Modules\\TradesModule\\src"
    //     }
    // },
    // git add src/; git commit -m "second commit";git push -u origin main
}
