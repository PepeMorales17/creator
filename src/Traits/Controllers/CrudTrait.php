<?php

namespace Pp\Creator\Traits\Controllers;

use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

trait CrudTrait
{
    private function menu()
    {
        return [];
    }

    public function justRenderIndex($dataTable, $props = [])
    {
        return Inertia::render($this->view, array_merge([
            'menu' => $this->menu(),
            'dataTable' => $dataTable
        ], $props));
    }

    public function justCreate($inputs, $props = [])
    {
        return Inertia::render($this->view, array_merge([
            'menu' => $this->menu(),
            'inputs' => $inputs
        ], $props));
    }

    public function justEdit($inputs, $item, $props = [])
    {
        return Inertia::render($this->view, array_merge([
            'menu' => $this->menu(),
            'inputs' => $inputs,
            'item' => $item
        ], $props));
    }

    public function justDestroy($model)
    {
        $model->delete();
        return $this->mainRedirect('Borrado');
    }

    public function justUpdate($model, $data)
    {
        $model->update($data);
        return $this->mainRedirect('Actualizado');
    }

    public function justShow($item, $props = [])
    {
        return Inertia::render($this->view, array_merge([
            'menu' => $this->menu(),
            'item' => $item
        ], $props));
    }

    public function mainRedirect($msg = 'Exito', $success = true, $params = [])
    {
        return Redirect::to(route($this->redirect_route,$params))->with($this->setFlash($success), $msg);
    }

    public function back($msg = null, $success = true)
    {
        return Redirect::back()->with($this->setFlash($success), $msg);
    }

    public function setFlash($success)
    {
        return $success ? 'success' : 'error';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->justCreate($this->form->getFormWithEmptyValue());

    }
}
