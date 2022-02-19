<?php

namespace Pp\Creator\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Crud
{


    //protected $makeHide = [];

    abstract function attrs();

    abstract static function menu();

    protected $optional = true;


    protected function attr($id, $type, $label = null, $props = [])
    {
        if ($id != 'id') {
            $label = $label ?? Str::studly($id);
        } else {
            $label = 'id';
        }
        $p = array_merge(
            [
                'optional' => $this->optional,
            ],
            $type === 'foreignId' ? [
                'relation_type' => 'belongsTo'
            ] : []
        );
        $props = array_merge($p, $props);

        return compact('id', 'label', 'type', 'props');
    }

    protected function externalRelations()
    {
        return [];
    }

    public function getRelations()
    {
        return Arr::where(array_merge($this->externalRelations(), $this->attrs()), fn ($i) => Arr::get($i, 'props.relation_type'));
    }

    public function getRelationName($id)
    {
        return Str::plural(str_replace('_id', '', $id));
    }

    protected function externalRelation($id, $type)
    {
        return $this->attr($id, null, null, ['relation_type' => $type]);
    }

    public function addTablesBeforeMigrate()
    {
        return [];
    }

    public function hidden()
    {
        return array_merge(collect($this->attrs())->reject(fn($i) => !Arr::get($i, 'props.modelHidden'))->pluck('id')->toArray(), [
            'created_at',
            'updated_at',
        ]);
    }

    // Comunes

    public function string($id, $label = null, $optional = null)
    {
        return $this->attr($id, 'string', $label, [
            'optional' => $this->isOptional($optional)
        ]);
    }

    public function inputName($optional = null)
    {
        return $this->attr('name', 'string', 'Nombre', [
            'optional' => $this->isOptional($optional)
        ]);
    }

    private function isOptional($optional)
    {
        return $optional === null ? $this->optional : $optional;
    }

    public function date($id = 'date', $label = 'Fecha', $optional = true)
    {
        return $this->attr($id, 'date', $label, [
            'optional' => $optional,
            'cast' => 'datetime:Y-m-d'
        ]);
    }

    public function datetime($id, $label = 'Fecha y hora', $optional = true)
    {
        return $this->attr($id, 'timestamp', $label, [
            'optional' => $optional,
            'cast' => 'datetime:Y-m-d\TH:i:s'
        ]);
    }

    public function approvedAt($optional = true, $id = 'approved_at', $label = 'Aprobado en')
    {
        return $this->attr($id, 'timestamp', $label, [
            'optional' => $optional,
            'cast' => 'datetime:Y-m-d\TH:i:s'
        ]);
    }

    public function relation($id, $label, $props = [])
    {
        return $this->attr($id, 'foreignId', $label, $props);
    }

    public function short($id, $label = null, $optional = true)
    {
        return $this->attr($id, 'string:short', $label, [
            'optional' => $optional,
        ]);
    }

    public function double($id, $label = null, $optional = true)
    {
        return $this->attr($id, 'double', $label, [
            'optional' => $optional,
        ]);
    }

    public function description($optional = true)
    {
        return $this->attr('description', 'text', 'Descripcion', [
            'optional' => $optional,
        ]);
    }

    public function note($optional = true)
    {
        return $this->attr('note', 'text', 'Nota', [
            'optional' => $optional,
        ]);
    }
}
