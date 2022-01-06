<?php

namespace Pp\Creator\Generates;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Crud
{


    protected $makeHide = [];

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
        return array_merge($this->makeHide, [
            'created_at',
            'updated_at',
        ]);
    }

    // Comunes

    public function string($id, $label = null, $optional = null)
    {
        return $this->attr($id, 'string', $label, [
            'optional' => $optional === null ? $this->optional : $optional
        ]);
    }

    public function inputName()
    {
        return $this->attr('name', 'string', 'Nombre');
    }
}
