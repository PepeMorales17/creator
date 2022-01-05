<?php

namespace Pp\Creator;


use Illuminate\Support\ServiceProvider;
use Pp\Creator\Generates\Commands\Crud\CreateAll;
use Pp\Creator\Generates\Commands\Crud\CreateController;
use Pp\Creator\Generates\Commands\Crud\CreateCreator;
use Pp\Creator\Generates\Commands\Crud\CreateCrud;
use Pp\Creator\Generates\Commands\Crud\CreateCrudVue;
use Pp\Creator\Generates\Commands\Crud\CreateForm;
use Pp\Creator\Generates\Commands\Crud\CreateMigration;
use Pp\Creator\Generates\Commands\Crud\CreateModel;

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
            __DIR__.'/resources/js' => base_path('resources/js')
        ], 'pp-js');

        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateAll::class,
                CreateController::class,
                CreateCreator::class,
                CreateCrud::class,
                CreateController::class,
                CreateCrudVue::class,
                CreateForm::class,
                CreateMigration::class,
                CreateModel::class,
            ]);
        }
    }

    // "autoload-dev": {
    //     "psr-4": {
    //         "Tests\\": "tests/",
    //         "Pp\\Trades\\": "Modules\\TradesModule\\src"
    //     }
    // },
}
