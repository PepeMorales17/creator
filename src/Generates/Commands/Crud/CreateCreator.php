<?php

namespace Pp\Creator\Generates\Commands\Crud;

use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CreateCreator extends GeneratorCommand
{
    use BaseTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:creator {name} {folder} {stub}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un archivo creador';

    protected $type = 'Creator';

    private $myPath = "Pp\Creator\Generates\Commands";
    private $stubDir = 'stubs\creator.stub';
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
        //$class = str_replace('{{namespace}}', $this->rootNamespace(), $class);
        $class = str_replace('{{namea}}', Str::studly($this->argument('name')), $class);
        $class = str_replace('{{command}}', $this->argument('name'), $class);
        $class = str_replace('{{stub}}', $this->argument('stub'), $class);

        return $class;
    }

    public function fileName()
    {
        return 'Create' . $this->studly() . '.php';

    }
}
