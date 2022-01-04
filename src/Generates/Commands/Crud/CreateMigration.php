<?php

namespace Pp\Creator\Generates\Commands\Crud;

use Pp\Creator\Generates\Commands\Traits\BaseTrait;
use Pp\Creator\Generates\Commands\Traits\MigrationTrait;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CreateMigration  extends GeneratorCommand
{

    use BaseTrait;
    use MigrationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:migration {name} {folder} {--force} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creo los archivos para el modelo';

    protected $type = 'Migration';


    private $myPath = 'database\migrations';
    private $stubDir = 'stubs\cruds\migrate.stub';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //dd($this->resolvePath());
        $dir = base_path($this->resolvePath()) . '\\' . $this->folder();
        if (is_dir($dir)) {

            $files = collect(scandir($dir))->filter(fn ($i) => strpos($i, $this->baseName()));

            if ($files->count() > 0) {
                if (!$this->option('force')) {
                    $this->error('Migracion ya existe');
                    return;
                } else {
                    $files->map(fn ($i) => $this->files->delete($dir . '\\' . $i));
                }
            }
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

        $class = str_replace('{{attrs}}', $this->resolveArray($this->attrs()), $class);
        $class = str_replace('{{name}}', Str::plural($this->argument('name')), $class);
        $class = str_replace('{{tablesBeforeMigrate:up}}', $this->resolveArray($this->tablesBeforeMigrate()), $class);
        $class = str_replace('{{tables:down}}', $this->resolveArray($this->tablesDown($this->argument('name'))), $class);

        return $class;
    }

    public function baseName()
    {
        return '_create_' . $this->argument('name') . '_table.php';
    }

    public function fileName()
    {
        return date('Y_m_d_His') . $this->baseName();
    }
}
