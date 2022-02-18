<?php

namespace Pp\Creator\Commands\Generates\Crud;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Composer;

class CreateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:all {name} {folder} {--force} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea controller, model, form, vue, migration';

    protected $composer;

    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arg = [
            'name' => $this->argument('name'),
            'folder' => $this->argument('folder'),
            '--force' => $this->option('force'),
            '--module' => $this->option('module'),
        ];
        $this->call('create:migration', $arg);
        $this->call('create:model', $arg);
        $this->call('create:form', $arg);
        $this->call('create:controller', $arg);
        $this->call('create:crud_vue', $arg);
        //$this->info('Dumping autoloads...');
        //$this->composer->dumpAutoloads();
        $this->info('Creados todos exitosamente' . ($this->option('module') ? ' en modulo ' . $this->option('module') : ''));
        //$this->warn('Run: php artisan migrate; php artisan create:menu');


        // dd([
        //     'creator.cruds' . str($this->argument('name'))->pluralStudly() => [
        //         'crud' => 'App\Creator\\' . $name,
        //         'model' => 'App\Models\\' . $name,
        //         'controller' => 'App\Http\Controllers\\' . $name,
        //         'form' => 'App\Forms\\' . $name,
        //         'vue' => str_replace('\\', '/', $name),
        //     ]
        // ]);
        // config([
        // "str($this->argument('name'))->pluralStudly() => [
        //     'crud' => 'App\Creator\\' . $name,
        //     'model' => 'App\Models\\' . $name,
        //     'controller' => 'App\Http\Controllers\\' . $name,
        //     'form' => 'App\Forms\\' . $name,
        //     'vue' => str_replace('\\', '/', $name),
        // ]"
        // ]);

        //dd($config);
        $this->updateConfig();
        $migration = $this->findMigrationName();
        //dd($migration);
        if ($this->option('force')) {
            $this->warn('exec... php artisan migrate:refresh --path=' . $migration . '  && php artisan create:menu');
            exec('php artisan migrate:refresh --path=' . $migration);
        } else {
            $this->warn('exec... php artisan migrate && php artisan create:menu');
            exec('php artisan migrate');
        }
        exec('php artisan create:menu');

        return 0;
    }

    public function updateConfig()
    {
        $dir = config_path('creator.php');
        if (!file_exists($dir)) {
            $this->error("El arhivo $dir no existe.");
            return;
        }
        $file = file_get_contents($dir);

        $name = str($this->argument('name'))->studly();
        $folder = str($this->argument('folder'))->studly();
        $folder = !!$folder ? $folder . '\\' : null;
        $name = $folder . $name;
        if (strpos($file, "App\Creator\\$name") !== false) return;
        $file = str_replace("/**CrudEnd */", "'" . str($this->argument('name'))->pluralStudly() . "' => [
            'crud' => 'App\Creator\\$name',
            'model' => 'App\Models\\$name',
            'controller' => 'App\Http\Controllers\\$name',
            'form' => 'App\Forms\\$name',
            'vue' => '" . str_replace('\\', '/', $name) . "',
        ],
        /**CrudEnd */", $file);
        file_put_contents($dir, $file);
        $this->info('Se agrego la clase al archivo config.creator');
    }

    public function findMigrationName()
    {
        $folder =  str($this->argument('folder'))->studly;
        $dir = base_path('database/migrations/') . '\\' . $folder;
        $folder = 'database/migrations/' . $folder;
        if (is_dir($dir)) {
            return $folder . '/' . collect(scandir($dir))->filter(fn ($i) => strpos($i, str($this->argument('name'))->pluralStudly()))->first();
        }
        return null;
    }
}
