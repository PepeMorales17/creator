<?php

namespace Pp\Creator\Http\Controllers;

use Pp\Creator\Forms\CreateMenuForm;
use App\Http\Controllers\Controller;
use Pp\Creator\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Pp\Creator\Traits\Controllers\CrudTrait;

class MenuController extends Controller
{
    use CrudTrait;

    protected $view = 'Creator/CrudMenu';
    protected $redirect_route = 'menu.index';
    protected $type = 'Creator';
    protected $folder = 'Creator';

    public function form()
    {
        return new CreateMenuForm();
    }

    public function menu()
    {
        return [
            'crud' => [
                'menu.index' => ['name' => 'Lista', 'route' => 'menu.index', 'title' => 'Listado de menus'],
                'menu.create' => ['name' => 'Crear', 'route' => 'menu.create', 'title' => 'Crear menu'],
            ]
        ];
    }


    public function __construct()
    {

        $this->middleware('web');
    }

    public function index()
    {

        return $this->justRenderIndex(Menu::view()->get());
    }

    public function store(Request $request)
    {
        $data = $this->form()->validate($request->all());

        DB::transaction(function () use ($data) {
            Menu::create($data);
        });

        return $this->mainRedirect('Menu guardado');
    }

    public function show($id)
    {
        $model = Menu::view()->findOrFail($id);
        return $this->justShow($model);
    }

    public function edit(Menu $menu)
    {
        return $this->justEdit($this->form()->getFormToEditWithEmptyValue(), $menu);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->form()->validate($request->all());
        DB::transaction(function () use ($data, $menu) {
            $menu->update($data);
        });

        return $this->mainRedirect('Menu editado');
    }

    public function destroy(Menu $menu)
    {
        DB::transaction(function () use ($menu) {
            $menu->delete();
        });
        return $this->mainRedirect('Menu borrado');
    }
}
