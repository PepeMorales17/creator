<?php

namespace Pp\Creator\Commands\Generates\Crud;

use Pp\Creator\Commands\Traits\BaseTrait;
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
        $curdClass = $this->getCrudClass();

        $rel = $this->setRelations($curdClass);

        $class = $this->replaceStr(parent::buildClass($name), [
            $this->replaceNamespaceInClass(),
            $this->replaceStudlyInClass(),
            $this->replaceTable(),
            ['{{fillable}}', $this->resolveArray(collect($this->getCrudClass()->attrs())->pluck('id')->reject(fn ($i) => $i === 'id')->values(), false)],
            ['{{hidden}}', $this->resolveArray($this->getCrudClass()->hidden(), false)],
            ['{{selects}}', $this->resolveArray($this->setSelects($curdClass), false)],
            ['{{relations}}', $this->resolveArray($rel)],
            ['{{casts}}', $this->resolveArray($this->setCast($curdClass), true)],
            //$this->buildRequired([]),
            ...$this->buildTraits()
        ]);

        // $class = parent::buildClass($name);
        // $class = str_replace('{{namespace}}', $this->rootNamespace(), $class); ok
        // $class = str_replace('{{namespaces}}', $this->resolveArray($rel[1]), $class);
        // $class = str_replace('{{name}}', Str::studly($this->argument('name')), $class); ok
        // ok $class = str_replace('{{fillable}}', $this->resolveArray(collect($this->getCrudClass()->attrs())->pluck('id')->reject(fn($i) => $i === 'id')->values(), false), $class);
        // ok $class = str_replace('{{hidden}}', $this->resolveArray($this->getCrudClass()->hidden(), false), $class);
        // ok $class = str_replace('{{selects}}', $this->resolveArray($this->setSelects(), false), $class);
        // $class = str_replace('{{relations}}', $this->resolveArray($rel[0]), $class);
        // ok $class = str_replace('{{table}}', $this->getTable(), $class);

        if ($this->getCrudClass()->hasFiles) {
            $class = str_replace('Illuminate\Database\Eloquent\Model', 'Pp\Creator\Models\MediaModel', $class);
            $class = str_replace('extends Model', 'extends MediaModel', $class);
        }



        return $class;
    }

    public function fileName()
    {
        return $this->studly() . '.php';
        //return date('Y_m_d_His') . '_create_' . $this->argument('name') . '_table.php';
    }

    public function setSelects($class)
    {
        return collect($class->attrs())->map(function ($item) {
            return $this->getTable() . '.' . $item['id'] . " as " . $item['label'];
        });
    }

    public function setRelations($class)
    {
        $relations = collect($class->getRelations());
        return $relations->map(function ($item) use ($class) {
            $trait = Arr::get($item, 'props.modelTrait');
            $folder = Arr::get($item, 'props.folder');
            $folder = $folder ?? $this->folder();
            if (!!$trait) {
                $this->traits[] = $trait;
                return null;
            };
            $relation =  $class->getRelationName($item['id']);
            $relationSingular =  Str::singular($class->getRelationName($item['id']));

            $infoClass = config("creator.cruds.$relation");

            $namespace = !!$infoClass ? $infoClass['model'] : $this->beginNamespaceModels($folder ? $folder.'\\' : null) . Str::studly($relationSingular);

            $funName = Str::{match (Arr::get($item, 'props.relation_type')) {
                'hasMany' => 'plural',
                default => 'singular'
            }}(Str::camel($relationSingular));

            $t = 'public function ' . $funName . '()
            {
                return $this->' . Arr::get($item, 'props.relation_type') . '(`' . $namespace . '`);
            }';
            return $t;
        })->filter()->values()->toArray();
    }

    public function setCast($class)
    {
        $relations = collect($class->attrs());
        $casts = [];
        foreach ($relations as $item) {
            $cast = Arr::get($item, 'props.cast');
            if (!$cast) continue;
            //$casts[Str::singular($class->getRelationName($item['id']))] = $cast;
            $casts[] = "'".Str::singular($class->getRelationName($item['id']))."' => '$cast',";
        }

        return $casts;
        // return $relations->map(function ($item) use ($class) {
        //     $cast = Arr::get($item, 'props.cast');
        //     if (!$cast) return null;

        //     $relation = Str::singular($class->getRelationName($item['id']));

        //     return [$relation => $cast];
        // })->filter()->values()->toArray();
    }
}
