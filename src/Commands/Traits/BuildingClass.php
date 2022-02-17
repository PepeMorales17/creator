<?php

namespace Pp\Creator\Commands\Traits;

use Illuminate\Support\Str;


trait BuildingClass
{

    use NamespacesTrait;

    private $traits = [];

    private function endLine($line)
    {
        return  $line . ';';
    }

    private function addLine($line)
    {
        return  $line . '\n';
    }

    private function resolveArray($array, $asString = true, $style = JSON_PRETTY_PRINT)
    {
        $str = json_encode($array, $style);
        if ($asString) {
            $str = $this->replaceStr($str, [
                [['"', '[', ']'], ''],
                [';,', ';'],
                ['\\\\', '\\'],
            ]);
        }
        if ($style === null) {
            $str = $this->replaceStr($str, [
                ['],', '],\n'],
                ['),', '),\n'],
            ]);
        }
        $str = $this->replaceStr($str, [
            [['*"', '"*'], ""],
            ['arrclose', "]"],
            ['arropen', "["],
            ['},', "}"],
            ['`', "'"],
            ['\n', " \r\n"],
        ]);

        return $str;
    }

    public function buildTraits()
    {
        //dd($this->traits);
        $traits =  collect($this->traits)->map(function ($t) {
            $name = explode('\\', $t);
            //dd($name, explode('\\', $t) );
            return [
                'namespace' => $t,
                'name' => 'use ' . $name[count($name) - 1] . ';',
            ];
        });

        //dd($traits, $traits->pick('namespace')->flatten(), $traits->pick('name')->flatten());
        return [
            $this->buildRequired($traits->pick('namespace')->flatten()->toArray(), '{{traits:required}}'),
            ['{{traits}}', $this->resolveArray($traits->pick('name')->flatten())]
        ];
    }


    private function buildRequired($values, $rep = '{{required}}')
    {
        $class = $this->getCrudClass();
        $values = array_merge($values, (isset($class->requiredClasses[$this->type]) ? $class->requiredClasses[$this->type] : []));
        return [$rep, $this->resolveArray(
            collect($values)->map(fn ($x) => 'use ' . $x . ';')->toArray()
        )];
    }

    private function replaceStr($str, $values, $clean = true)
    {
        foreach ($values as $replace) {
            $str = str_replace($replace[0], $replace[1], $str);
        }

        if ($clean) $str = preg_replace('/{{.*}}/m', '', $str);

        return $str;
    }

    private function replaceNamespaceInClass()
    {
        return ['{{namespace}}', $this->rootNamespace()];
    }

    private function replacePluralInClass()
    {
        return ['{{name:plural}}', Str::studly(Str::plural($this->argument('name')))];
    }

    private function replaceStudlyInClass()
    {
        return ['{{name:studly}}', Str::studly($this->argument('name'))];
    }

    private function replaceNameInClass()
    {
        return ['{{name}}', $this->argument('name')];
    }

    private function replaceNameCamelInClass()
    {
        return ['{{name:camel}}', Str::camel($this->argument('name'))];
    }

    private function replaceFolder()
    {
        return ['{{folder}}', $this->folder()];
    }

    private function replaceTable()
    {
        return ['{{table}}', $this->getTable()];
    }
}
