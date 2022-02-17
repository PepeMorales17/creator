<?php

namespace Pp\Creator\Commands\Traits;

use Illuminate\Support\Str;


trait PathTrait
{
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
        return dirname(__DIR__, 3) . '\\' . trim($stub, '/');
    }


    protected function rootNamespace()
    {
        $name = $this->resolvePath() . ($this->folder() ? "\\" . $this->folder() : null);
        return $this->hasModule() ? str_replace('App' . $this->module() . 'src\\', 'Pp\\' . Str::studly($this->option('module')) . '\\', $name)
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

    private function hasModule()
    {
        return !!$this->option('module');
    }

    private function module()
    {
        return $this->hasModule() ? "\Modules\\" . Str::studly($this->option('module')) . "Module\\" : '';
    }

    protected function resolvePath()
    {
        $path = $this->myPath;
        if (strpos($path, 'App\\') === false && $this->hasModule()) {
            $path = 'App\\' . $path;
        }
        if ($this->hasModule()) {
            $path = str_replace('App\\', 'App' . $this->module() . 'src\\', $path); //'Pp\\'.$this->module(), $path);
        }
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
        return base_path($this->resolvePath() . ($this->folder() ? "\\" . $this->folder() : null) . '\\') . $this->fileName();
    }

    public function folder()
    {
        if ($this->argument('folder') === 'null') return null;
        return Str::studly($this->argument('folder'));
    }

    public function fileName()
    {
        return $this->studly() . '.' . $this->extension;
    }
}
