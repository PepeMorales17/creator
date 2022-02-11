<?php

namespace Pp\Creator\Commands\Traits;

use Illuminate\Support\Str;


trait BaseTrait
{
    private function endLine($line)
    {
        return  $line . ';';
    }

    private function addLine($line)
    {
        return  $line . '\n';
    }


    private function getCrudClass()
    {
        $class = $this->getCrudNamespace();
        if (!class_exists($class)) {
            $class = 'App\Creator\Cruds\\'.($this->folder() ? $this->folder()."\\" : null ) . Str::studly($this->getTable()) . 'Crud';
        }
        if(!class_exists($class)){
            throw new \Exception('CrudClass not found');
        }
        return new $class();
    }

    public function getCrudNamespace()
    {
        return 'Pp\Creator\Cruds\\'.($this->folder() ? $this->folder()."\\" : null ) . Str::studly($this->getTable()) . 'Crud';
    }

    public function getTable()
    {
        return Str::plural($this->argument('name'));
    }

    public function studly()
    {
        return Str::studly($this->argument('name'));
    }


    private function resolveArray($array, $asString = true, $style = JSON_PRETTY_PRINT)
    {
        $str = json_encode($array, $style);
        if ($asString) {
            $str = str_replace(
                ['"', '[', ']'],
                '',
                $str
            );
            $str = str_replace(';,', ';', $str);
            $str = str_replace('\\\\','\\', $str);
        }
        if ($style === null ){
            $str = str_replace('],', '],\n', $str);
            $str = str_replace('),', '),\n', $str);
        }
        $str = str_replace(['*"', '"*'], "", $str);
        $str = str_replace('arrclose', "]", $str);
        $str = str_replace('arropen', "[", $str);
        $str = str_replace('},', "}", $str);
        $str = str_replace('\n', " \r\n", $str);
        return $str;
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath($this->stubDir);
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        //dd(app_path(trim($stub, '/')));
        //C:\xampp\htdocs\laravel\trading\trading\Pp\Creatorstubs\cruds\controller.stub
        // return file_exists($customPath = app_path(trim($stub, '/')))
        //     ? $customPath
        //     : app_path($stub);

        return dirname(__DIR__,3).'\\'.trim($stub, '/');
    }


    protected function rootNamespace()
    {
        $name = $this->resolvePath().($this->folder() ? "\\".$this->folder() : null );
        // dd($this->hasModule() ? str_replace('App'.$this->module().'src\\', 'Pp\\'.Str::studly($this->option('module')), $name)
        // : 'no');
        //dd('ep');
        return $this->hasModule() ? str_replace('App'.$this->module().'src\\', 'Pp\\'.Str::studly($this->option('module')).'\\', $name)
        : $name;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    private function hasModule() {
        return !!$this->option('module');
    }

    private function module() {
        return $this->hasModule() ? "\Modules\\".Str::studly($this->option('module'))."Module\\" : '';
        //return Str::studly($this->option('module'));
    }

    protected function resolvePath()
    {
        $path = $this->myPath;
        //dd(strpos($path, 'App\\') === false, $path);
        if (strpos($path, 'App\\') === false && $this->hasModule()) {
            $path = 'App\\'.$path;
            //dd($path, 'a');
        }
        if ($this->hasModule()) {
            $path = str_replace('App\\', 'App'.$this->module().'src\\', $path); //'Pp\\'.$this->module(), $path);
        }
        //dd($path);
        return $path;

    }


    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        //dd( base_path($this->myPath.($this->folder() ? "\\".$this->folder() : null ) . '\\') . $this->fileName());
        return base_path($this->resolvePath().($this->folder() ? "\\".$this->folder() : null ) . '\\') . $this->fileName();
    }

    public function folder()
    {
        if ($this->argument('folder') === 'null') return null;
        return Str::studly($this->argument('folder'));
    }

    public function fileName()
    {
        return $this->studly().'.'.$this->extension;
    }
}
