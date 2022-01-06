<?php

namespace Pp\Creator\Generates\Commands\Crud;

use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CreateModel extends GeneratorCommand
{
    use BaseTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:model {name} {folder} {--force} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creo los archivos para el modelo';

    protected $type = 'Model';


    private $myPath = "App\Models";
    private $stubDir = 'stubs\cruds\model.stub';
    private $extension = 'php';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->getCrudClass()->run()['model']) {
            $this->info('No se creara el modelo');
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
        $rel =$this->setRelations();
        $class = str_replace('{{namespace}}', $this->rootNamespace(), $class);
        $class = str_replace('{{namespaces}}', $this->resolveArray($rel[1]), $class);
        $class = str_replace('{{name}}', Str::studly($this->argument('name')), $class);
        $class = str_replace('{{fillable}}', $this->resolveArray(collect($this->getCrudClass()->attrs())->pluck('id')->reject(fn($i) => $i === 'id')->values(), false), $class);
        $class = str_replace('{{hidden}}', $this->resolveArray($this->getCrudClass()->hidden(), false), $class);
        $class = str_replace('{{selects}}', $this->resolveArray($this->setSelects(), false), $class);
        $class = str_replace('{{relations}}', $this->resolveArray($rel[0]), $class);
        $class = str_replace('{{table}}', $this->getTable(), $class);

        return $class;
    }

    public function fileName()
    {
        return $this->studly().'.php';
        //return date('Y_m_d_His') . '_create_' . $this->argument('name') . '_table.php';
    }

    public function setSelects()
    {
        return collect($this->getCrudClass()->attrs())->map(function($item) {
            return $this->getTable().'.'.$item['id']." as ".$item['label'];
        });
    }

    public function setRelations()
    {
        $class = $this->getCrudClass();
        $namespaces = [];
        $relations = collect($class->getRelations())->map(function ($item) use ($class, &$namespaces) {
            $t = "public function {{name}}()
            {
                return " . '$this' . "->{{relation_type}}({{namespace}});
            }";
            $name =  Str::singular($class->getRelationName($item['id']));
            $namespaces[] = 'use '.(Arr::get($item, 'props.namespace') ?? Str::studly($this->rootNamespace()). "\\" . Str::studly($name)).';';
            $t = str_replace('{{name}}', Str::{
                match(Arr::get($item, 'props.relation_type')) {
                    'hasMany' => 'plural',
                    default => 'singular'
                }
            }(Str::camel($name)), $t);
            $t = str_replace('{{relation_type}}', Arr::get($item, 'props.relation_type'), $t);
            // $t = str_replace(
            //     '{{namespace}}',
            //     $namespace,
            //     $t
            // );
            $t = str_replace(
                '{{namespace}}',
                Str::studly($name).'::class',
                $t
            );
            return $t;
        })->values()->toArray();
        return [$relations, $namespaces];
    }
}
