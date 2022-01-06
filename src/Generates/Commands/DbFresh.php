<?php

namespace Pp\Creator\Generates\Commands;


use Illuminate\Console\Command;
use Pp\Creator\Models\Menu;

class DbFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh -{-seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $menus = Menu::all();
        $this->call('migrate:fresh', [
            '--seed' => $this->option('seed')
        ]);
        Menu::insert($menus->toArray());
        $this->info('El menu fue reestablecido');
        return 0;
    }
}
