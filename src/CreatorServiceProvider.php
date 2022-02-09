<?php

namespace Pp\Creator;


use Illuminate\Support\ServiceProvider;
use Pp\Creator\Generates\Commands\CreateMenu;
use Pp\Creator\Generates\Commands\CreatorInit;
use Pp\Creator\Generates\Commands\Crud\CreateAll;
use Pp\Creator\Generates\Commands\Crud\CreateController;
use Pp\Creator\Generates\Commands\Crud\CreateCreator;
use Pp\Creator\Generates\Commands\Crud\CreateCrud;
use Pp\Creator\Generates\Commands\Crud\CreateCrudVue;
use Pp\Creator\Generates\Commands\Crud\CreateForm;
use Pp\Creator\Generates\Commands\Crud\CreateMigration;
use Pp\Creator\Generates\Commands\Crud\CreateModel;
use Pp\Creator\Generates\Commands\DbFresh;

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
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/resources/js' => base_path('resources/js'),
            __DIR__.'/resources/Pages' => base_path('resources/js/Pages'),
            __DIR__.'/resources/AppLayout.vue' => base_path('resources\js\Layouts\AppLayout.vue'),
            __DIR__.'/resources/css' => base_path('resources\css'),
            __DIR__.'/resources/main-folder' => base_path(''),
            __DIR__.'/config/menus.php' => config_path('menus.php'),
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
