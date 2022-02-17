<?php

namespace Pp\Creator\Commands\Traits;



trait NamespacesTrait
{
    public function beginNamespaceController($folder = null)
    {
        $folder = $folder ?? $this->folder();
        return 'App\Http\Controllers'.($folder ? "\\" . $folder : null);
    }

    public function beginNamespaceModels($folder = null)
    {
        $folder = $folder ?? $this->folder();
        return 'App\Models'.($folder ? "\\" . $folder : null);
    }

    public function beginNamespaceForms($folder = null)
    {
        $folder = $folder ?? $this->folder();
        return 'App\Forms'.($folder ? "\\" . $folder : null);
    }

    public function modelName()
    {
        return $this->studly();
    }

    public function formName()
    {
        return 'Create' . $this->studly() . 'Form';
    }

    public function controllerName()
    {
        return  $this->studly() . 'Controller';
    }

    public function fullNamespaceModel()
    {
        return $this->beginNamespaceModels().'\\'.$this->modelName();
    }

    public function fullNamespaceForm()
    {
        return $this->beginNamespaceForms().'\\'.$this->formName();
    }

    public function fullNamespaceController()
    {
        return $this->beginNamespaceController().'\\'.$this->controllerName();
    }
}
