<?php

namespace Pp\Creator\Generates\Commands\Crud;

use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CreateForm extends GeneratorCommand
{
    use BaseTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:form {name} {folder} {--force} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creo los archivos para el formulario';

    protected $type = 'Form';

    private $myPath = "App\Forms";
    private $stubDir = 'stubs\cruds\create_form.stub';
    private $extension = 'php';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->getCrudClass()->run()['form']) {
            $this->info('No se creara el form');
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
        $class = str_replace('{{name}}', Str::studly($this->argument('name')), $class);
        $class = str_replace('{{inputs}}', $this->resolveArray($this->setInputs(), false, null), $class);
        $class = str_replace('{{validations}}', $this->resolveArray($this->validations()), $class);


        return $class;
    }

    public function fileName()
    {
        return 'Create' . $this->studly() . 'Form.php';
        //return date('Y_m_d_His') . '_create_' . $this->argument('name') . '_table.php';

    }

    public function setInputs()
    {
        $class = $this->getCrudClass();
        $relations = collect($class->attrs())->reject(fn ($i) => $i['id'] === 'id')->map(function ($item) use ($class) {
            $t = $this->setInputBy($item['id']);
            if (!!$t) return $t;
            $t = $this->setInputByType($item);
            return $t;
        })->values()->toArray();
        //dd($relations);
        return $relations;
    }

    private function setInputBy($id)
    {
        return match($id) {
            'ticker' => '*$this->select'."('ticker', 'Ticker', 'tickers')*",
            default => null
        };
    }

    private function setInputByType($input)
    {
        return match($input['type']) {
            'foreignId' => '*$this->select'."('".$input['id']."', '".$input['label']."')*",
            'string' => [$input['id'], $input['label'], 'input', 'text'],
            'string:short' => [$input['id'], $input['label'], 'input', 'text:short'],
            'double' => [$input['id'], $input['label'], 'input', 'double'],
            'file' => [$input['id'], $input['label'], 'input', 'file'],
            'text' => [$input['id'], $input['label'], 'text', 'text'],
            'integer' => [$input['id'], $input['label'], 'input', 'number'],
            'timestamp' => [$input['id'], $input['label'], 'input', 'datetime'],
            'date' => [$input['id'], $input['label'], 'input', 'date'],
            default => null,
        };
    }

    private function validations()
    {
        $rules = [];
        collect($this->getCrudClass()->attrs())->map(function($item) use(&$rules) {
            $rules[] = "'".$item['id']."' => '".(!!Arr::get($item,'props.optional') ? 'nullable' : 'required')."'";
        });

        return $rules;
    }
}
