<?php

namespace Pp\Creator\Generates\Commands\Crud;

use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $this->getCrudClass();

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
        $class = str_replace('{{name}}', Str::studly($this->argument('name')), $class);
        $class = str_replace('{{name:camel}}', Str::camel($this->argument('name')), $class);
        $class = str_replace('{{useModel}}', 'use ' . str_replace('Http\Controllers', 'Models', $this->rootNamespace()) . '\\' . $this->studly(), $class);
        $class = str_replace('{{useForm}}', 'use ' . str_replace('Http\Controllers', 'Forms', $this->rootNamespace()) . '\Create' . $this->studly() . 'Form', $class);
        $class = str_replace('{{folder}}', $this->folder(), $class);
        return $class;
    }

    protected function createMenu()
    {
        DB::table('menus')->create([
            $this->getCrudClass()->menu()
        ]);
    }

    public function fileName()
    {
        return $this->studly() . 'Controller.php';
    }
}
