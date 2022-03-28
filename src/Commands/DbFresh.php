<?php

namespace Pp\Creator\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Pp\Creator\Models\Menu;

class DbFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh {--seed}';

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
        Cache::forget('menus_recover');
        Cache::rememberForever('menus_recover', function () {
            return config('creator.class_menu')::all()->toArray();
        });
        $this->call('migrate:fresh', [
            '--seed' => $this->option('seed')
        ]);
        config('creator.class_menu')::insert(Cache::get('menus_recover'));
        $this->info('El menu fue reestablecido');
        return 0;
    }
}
