<?php

namespace Pp\Creator\Generates\Commands\Crud;

use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CreateCrudVue extends GeneratorCommand
{
    use BaseTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:crud_vue {name} {folder} {--force} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un archivo CrudVue';

    protected $type = 'CrudVue';

    private $myPath = "Resources\Js\Pages";
    private $stubDir = 'stubs\cruds\crud_vue.stub';
    private $extension = 'vue';


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

        return $class;
    }

    public function fileName()
    {
        return 'Crud'.$this->studly().'.vue';
        //return date('Y_m_d_His') . '_create_' . $this->argument('name') . '_table.vue';

    }

    private function module() {
        return $this->option('module') ? "\Modules\\".Str::studly($this->option('module'))."Module\\" : '';
    }
}
