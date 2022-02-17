<?php

namespace Pp\Creator\Commands\Traits;
use Illuminate\Support\Str;




trait AttrTrait
{
    public static function attr($id, $type, $label = null, $props = [])
    {
        if ($id != 'id') {
            $label = $label ?? Str::studly($id);
        } else {
            $label = 'id';
        }
        $p = array_merge(
            $type === 'foreignId' ? [
                'relation_type' => 'belongsTo'
            ] : []
        );
        $props = array_merge($p, $props);

        return compact('id', 'label', 'type', 'props');
    }
}
