<?php

namespace Pp\Creator\Generates\Commands\Crud;


use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CreateCrud extends GeneratorCommand
{
    use BaseTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:crud {name} {folder=null} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un archivo Crud';

    protected $type = 'Crud';

    private $myPath = "App\Creator\Cruds";
    private $stubDir = 'stubs\cruds\crud.stub';
    private $extension = 'php';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
        $class = str_replace('{{name:plural}}', Str::studly(Str::plural($this->argument('name'))), $class);
        $class = str_replace('{{name:studly}}', Str::studly($this->argument('name')), $class);
        $class = str_replace('{{name}}', $this->argument('name'), $class);

        return $class;
    }

    public function fileName()
    {
        return Str::studly(Str::plural($this->argument('name'))).'Crud.php';
        // return date('Y_m_d_His') . '_create_' . $this->argument('name') . '_table.php';

    }
}
