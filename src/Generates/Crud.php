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
        $label = $label ?? Str::studly($id);
        $p = array_merge(
            [
                'optional' => $this->optional,
            ],
            $type === 'foreignId' ? [
                'relation_type' => 'belongsTo'
            ] : []
        );
        $props = array_merge($props, $p);
        return compact('id', 'label', 'type', 'props');
    }

    protected function externalRelations() {
        return [];
    }

    public function getRelations()
    {
        return Arr::where(array_merge($this->externalRelations(),$this->attrs()), fn ($i) => Arr::get($i, 'props.relation_type'));
    }

    public function getRelationName($id)
    {
        return Str::plural(str_replace('_id', '', $id));
    }

    protected function externalRelation($id, $type) {
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
}
