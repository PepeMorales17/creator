<?php

namespace Pp\Creator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Pp\Creator\Models\Menu;

class CreateMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:menu {--s}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea el menu de las clases';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $menus;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Se eliminara la informacion de la tabla de menus');
        $this->menus = collect([]);

        DB::table(config('creator.table_munu'))->truncate();
        DB::transaction(function () {
            //dd(config('menus'));
            collect(config('menus'))->map(function ($route, $class) {
                if (is_string($route) || $route === null) {
                    $menu = !!$route ? $this->find($route) : null;
                    $menusCreated = $class::menu($menu ? $menu->id : null);
                    if (!!$menusCreated) $this->menus->push(...$menusCreated);
                }

                if (is_array($route)) {
                    $this->menus->push(config('creator.class_menu')::create($route));
                }

            });
        });
        $this->info('Se crearon los menus');
        $this->call('cache:clear');
        if ($this->option('s')) {
            $this->call('serve');
        }
        return 0;
    }

    public function find($namaspace)
    {
        return $this->menus->where('namespace', $namaspace)->first();
    }
}
