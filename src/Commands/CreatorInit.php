<?php

namespace Pp\Creator\Commands;

use Illuminate\Console\Command;

class CreatorInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'creator:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura laravel para el uso del paquete';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->finalCommands();
        $this->addToFilesystems();
        $this->modifyMedia();
        $this->configInertiaProps();
        $this->configBootstrapJs();
        $this->configAppJs();
        $this->allowMigrationSubFolders();
        $this->info('Publicando...');
        $this->call('vendor:publish', [
            '--tag' => 'pp-creator',
            '--force' => true
        ]);
        exec('npm install');
        exec('npm run dev');
        $this->call('migrate');

        $this->warn('En este archivo puedes encontrar la configuracion: ' . config_path('creator.php'));
        $this->info('Ejecutando los procesos e instalando dependencias');
        // $this->warn('Para que la template funcione: npm i @ppjmorales/creator_template');
        // $this->warn('Tambien agrega a Packege.json: "postcss-advanced-variables": "^3.0.1"');
        // $this->warn("Agrega: public_path('images') => storage_path('app/images'), en config\filesystems.php");
        // $this->warn('file manager:
        // composer require spatie/laravel-medialibrary; php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations";
        // php artisan migrate; php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config";');
        // //Si agrego para que use archivos tengo que hacer el comando para que copie la configuracion que voy a usar ademas tengo que agregar un tabla de folders
        // // agregar las rutas
        // $this->call('storage:link');
        // $this->info('No olvides cambiar la ulr de ENV para que funcione el storage');
        return 0;
    }

    public function allowMigrationSubFolders()
    {
        $dir = app_path('Providers\AppServiceProvider.php');
        if (!file_exists($dir)) {
            $this->error('El arhivo Providers\AppServiceProvider.php no existe.');
            return;
        }
        $file = file_get_contents($dir);
        //     dd(strpos($file, 'public function boot()
        // {'));
        $file = str_replace('public function boot()
    {', 'public function boot()
            {
                $mainPath = database_path("migrations");
        $directories = glob($mainPath . "/*" , GLOB_ONLYDIR);
        $paths = array_merge([$mainPath], $directories);

        $this->loadMigrationsFrom($paths);', $file);
        file_put_contents($dir, $file);
        $this->info($dir . ' Publicado');
    }

    public function configInertiaProps()
    {
        $dir = app_path('Http\Middleware\HandleInertiaRequests.php');
        if (!file_exists($dir)) {
            $this->error('El arhivo Http\Middleware\HandleInertiaRequests.php no existe checa si lo tienes instalado.');
            return;
        }
        $file = file_get_contents($dir);
        $file = str_replace('use Inertia\Middleware;', 'use Inertia\Middleware;
        use Pp\Creator\Models\Menu;
        use Illuminate\Support\Str;
        ', $file);
        $file = str_replace('return array_merge(parent::share($request), [', 'return array_merge(parent::share($request), [
    "main_menu" => Menu::tree(),
    "flash" => function () use ($request) {
        return [
            "success" => $request->session()->get("success"),
            "error" => $request->session()->get("error"),
        ];
    },
    "title" => Str::studly(str_replace(".", "_", $request->route()->getName())),
    ', $file);
        file_put_contents($dir, $file);
        $this->info($dir . ' Publicado');
    }

    public function configBootstrapJs()
    {
        $dir = base_path('resources\js\bootstrap.js');
        if (!file_exists($dir)) {
            $this->error('El arhivo resources\js\bootstrap.js no existe checa si lo tienes instalado.');
            return;
        }
        $file = file_get_contents($dir);
        $file .= 'import "./libs";';
        file_put_contents($dir, $file);
        $this->info($dir . ' Publicado');
    }

    public function configAppJs()
    {
        $dir = base_path('resources\js\app.js');
        if (!file_exists($dir)) {
            $this->error('El arhivo resources\js\app.js no existe checa si lo tienes instalado.');
            return;
        }
        $file = file_get_contents($dir);
        $file = str_replace("import { InertiaProgress } from '@inertiajs/progress';", "import { InertiaProgress } from '@inertiajs/progress';
        import global from './Plugins/global';", $file);
        $file = str_replace(".use(plugin)", ".use(plugin)
        .use(global)", $file);
        file_put_contents($dir, $file);
        $this->info($dir . ' Publicado');
    }

    public function finalCommands()
    {
        exec('composer require laravel/breeze --dev');
        sleep(1);
        $this->call('breeze:install', [
            'stack' => 'vue'
        ]);
        sleep(1);
        exec('composer require laravel/fortify');
        sleep(1);
        $this->call('vendor:publish', [
            '--provider' => "Laravel\Fortify\FortifyServiceProvider",
        ]);
        sleep(1);
        exec('composer require spatie/laravel-medialibrary');
        sleep(1);
        exec('npm i @ppjmorales/creator_template');
        sleep(1);
        $this->call('vendor:publish', [
            '--provider' => "Spatie\MediaLibrary\MediaLibraryServiceProvider",
            '--tag' => "migrations",
        ]);
        $this->call('vendor:publish', [
            '--provider' => "Spatie\MediaLibrary\MediaLibraryServiceProvider",
            '--tag' => "config",
        ]);
    }

    public function addToFilesystems()
    {
        $dir = config_path('filesystems.php');
        if (!file_exists($dir)) {
            $this->error('El arhivo ' . config_path('filesystems.php') . ' no existe checa si lo tienes instalado.');
            return;
        }
        $file = file_get_contents($dir);

        $file = str_replace("'disks' => [", "'disks' => [
            'media' => [
                'driver' => 'local',
                'root'   => storage_path('app/media'),
                'url'    => env('APP_URL').'/media',
                'visibility' => 'public',
            ],", $file);

        $file = str_replace("public_path('storage') => storage_path('app/public'),", "
            public_path('storage') => storage_path('app/public'),
            public_path('images') => storage_path('app/images'),
            public_path('media') => storage_path('app/media'),
        ],", $file);

        file_put_contents($dir, $file);
        $this->info($dir . ' Modificado');
    }

    public function modifyMedia()
    {
        $dir = config_path('media-library.php');
        if (!file_exists($dir)) {
            $this->error('El arhivo ' . config_path('media-library.php') . ' no existe checa si lo tienes instalado.');
            return;
        }
        $file = file_get_contents($dir);

        $file = str_replace("'disk_name' => env('MEDIA_DISK', 'public'),", "'disk_name' => 'media',", $file);


        file_put_contents($dir, $file);

        $this->info($dir . ' Modificado');
    }
}
