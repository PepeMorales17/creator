<?php

namespace Pp\Creator\Generates\Commands\Crud;

use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Pp\Creator\Models\Menu;

class CreateController extends GeneratorCommand
{
    use BaseTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:controller {name} {folder} {--force} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un archivo Controller';

    protected $type = 'Controller';

    private $myPath = "App\Http\Controllers";
    private $stubDir = 'stubs\cruds\controller.stub';
    private $extension = 'php';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->getCrudClass()->run()['controller']) {
            $this->info('No se creara el controlador');
            return;
        }

        if (parent::handle() === false && !$this->option('force')) {
            return;
        }
        return 0;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $class = parent::buildClass($name);
        $class = str_replace('{{namespace}}', $this->rootNamespace(), $class);
        $class = str_replace('{{name:normal}}', $this->argument('name'), $class);
        $class = str_replace('{{name}}', Str::studly($this->argument('name')), $class);
        $class = str_replace('{{name:plural}}', Str::plural(Str::studly($this->argument('name'))), $class);
        $class = str_replace('{{name:camel}}', Str::camel($this->argument('name')), $class);
        $class = str_replace('{{useModel}}', 'use ' . str_replace('Http\Controllers', 'Models', $this->rootNamespace()) . '\\' . $this->studly(), $class);
        $class = str_replace('{{useForm}}', 'use ' . str_replace('Http\Controllers', 'Forms', $this->rootNamespace()) . '\Create' . $this->studly() . 'Form', $class);
        $class = str_replace('{{folder}}', $this->folder(), $class);
        $this->createMenu();
        $this->updateRoute();
        return $class;
    }

    protected function createMenu()
    {
        if (!Menu::where('namespace', $this->argument('name') . '.index')->exists()) {
            $this->getCrudClass()->menu();
        }
    }

    public function fileName()
    {
        return $this->studly() . 'Controller.php';
    }

    protected function updateRoute()
    {
        $dir = base_path('routes\web.php');
        if (!file_exists($dir)) {
            $this->error('El arhivo routes\web.php no existe.');
            return;
        }
        $file = file_get_contents($dir);
        $name = "'" . $this->argument('name') . "'";
        $class = "'" . $this->rootNamespace() . "\\" . Str::studly($this->argument('name')) . "Controller'";

        if (strpos($file, ']);/** Final de los controladores */') !== false) {
            $route = "$name => $class,";
            if (strpos($file, $route) === false) {
                $file = str_replace(']);/** Final de los controladores */', "\t".$route."\r\n]);/** Final de los controladores */", $file);
                file_put_contents($dir, $file);
                $this->info("Se agrego la ruta en web.php");
                return;
            }
            $this->info("Ya existe la ruta en web.php");
            return;
        }

        $route = "Route::resource($name, $class);";
        if (strpos($file, $route) === false) {
            $file .= "\r\n " . $route;
            file_put_contents($dir, $file);
            $this->info("Se agrego la ruta en web.php");
            return;
        }
        $this->info("Ya existe la ruta en web.php");
    }
}
