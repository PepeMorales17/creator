<?php

namespace Pp\Creator\Commands\Generates\Crud;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;

class CreateAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:all {name} {folder} {--force} {--module=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea controller, model, form, vue, migration';

    protected $composer;

    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arg = [
            'name' => $this->argument('name'),
            'folder' => $this->argument('folder'),
            '--force' => $this->option('force'),
            '--module' => $this->option('module'),
        ];
        $this->call('create:migration', $arg);
        $this->call('create:model', $arg);
        $this->call('create:form', $arg);
        $this->call('create:controller', $arg);
        $this->call('create:crud_vue', $arg);
        //$this->info('Dumping autoloads...');
        //$this->composer->dumpAutoloads();
        $this->info('Creados todos exitosamente'.($this->option('module') ? ' en modulo '.$this->option('module') : ''));
        $this->warn('Run: php artisan create:menu');

        return 0;
    }

}
