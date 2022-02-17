<?php

namespace Pp\Creator\Commands\Generates\Crud;

use Pp\Creator\Commands\Traits\BaseTrait;
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
        $class = $this->replaceStr(parent::buildClass($name), [
            $this->replaceNamespaceInClass(),
            $this->replacePluralInClass(),
            $this->replaceStudlyInClass(),
            $this->replaceNameInClass(),
            $this->replaceNameCamelInClass(),
            $this->buildRequired([
                $this->fullNamespaceForm(),
                $this->fullNamespaceModel()
            ]),
            $this->replaceFolder(),
        ]);


        $this->createMenu();
        $this->updateRoute();
        $this->updateMenu();

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

        if (strpos($file, 'Route::resources([') === false) {
            $file .= "\r\n " . $this->routeResources();
        }

        if (strpos($file, ']);// Final de los controladores */') !== false) {
            $route = "$name => $class,";
            if (strpos($file, $route) === false) {
                $file = str_replace(']);// Final de los controladores */', "\t" . $route . "\r\n]);// Final de los controladores */", $file);
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

    public function routeResources()
    {
        return "Route::resources([
        ]);// Final de los controladores */";
    }

    public function updateMenu()
    {
        $dir = config_path('menus.php');
        if (!file_exists($dir)) {
            $this->error("El arhivo $dir no existe.");
            return;
        }
        $file = file_get_contents($dir);

        $find = '];';

        if (strpos($file, $this->getCrudNamespaceFromApp()) === false) {
            $file = str_replace($find, "'" . $this->getCrudNamespaceFromApp() . "' => '', \r\n $find", $file);
            file_put_contents($dir, $file);
        }


        $this->info("Se agrego el menu");
    }
}
