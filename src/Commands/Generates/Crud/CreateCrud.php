<?php

namespace Pp\Creator\Commands\Generates\Crud;


use Pp\Creator\Commands\Traits\BaseTrait;
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
    protected $signature = 'create:crud {name} {folder=null} {--module=} {--force}';

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
        return $this->replaceStr(parent::buildClass($name), [
            $this->replaceNamespaceInClass(),
            $this->replacePluralInClass(),
            $this->replaceStudlyInClass(),
            $this->replaceNameInClass(),
        ]);

    }

    public function fileName()
    {
        return Str::studly(Str::plural($this->argument('name'))).'Crud.php';
        // return date('Y_m_d_His') . '_create_' . $this->argument('name') . '_table.php';

    }
}
