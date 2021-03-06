<?php

namespace Pp\Creator\Generates\Commands;

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
        $this->configInertiaProps();
        $this->configAppJs();
        $this->allowMigrationSubFolders();
        $this->info('Publicando...');
        $this->call('vendor:publish', [
            '--tag' => 'pp-creator',
            '--force' => true
        ]);
        $this->info('Run npm install @heroicons/vue');
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
        $this->info($dir.' Publicado');
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
    "title" => Str::studly(str_replace(".", "_", $request->route()->getName()))
    ', $file);
        file_put_contents($dir, $file);
        $this->info($dir.' Publicado');
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
        $this->info($dir.' Publicado');
    }
}
