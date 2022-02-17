<?php

namespace Pp\Creator\Commands\Traits;

use Illuminate\Support\Str;


trait CrudClassTrait
{
    private function getCrudClass()
    {
        $class = $this->getCrudNamespaceFromCreator();
        if (!class_exists($class)) {
            $class = $this->getCrudNamespaceFromApp();
        }
        if (!class_exists($class)) {
            throw new \Exception('CrudClass not found');
        }
        return new $class();
    }

    private function getCrudNamespaceFromCreator()
    {
        return 'Pp\Creator\Cruds\\' . ($this->folder() ? $this->folder() . "\\" : null) . Str::studly($this->getTable()) . 'Crud';
    }

    private function getCrudNamespaceFromApp()
    {
        return 'App\Creator\Cruds\\' . ($this->folder() ? $this->folder() . "\\" : null) . Str::studly($this->getTable()) . 'Crud';
    }

    private function getTable()
    {
        return Str::plural($this->argument('name'));
    }

    private function studly()
    {
        return Str::studly($this->argument('name'));
    }
}
