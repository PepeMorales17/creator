<?php

namespace Pp\Creator\Forms;

use Illuminate\Support\Facades\Validator;
use Pp\Creator\Traits\Inputs\InputTrait;


class CreateMenuForm
{
    use InputTrait;


    public function getFormWithEmptyValue()
    {
        $inputs = [
            ["name", "Nombre", "input", "text"],
            ["icon", "Icono", "input", "text:short"],
            ["description", "Descripcion", "input", "text"],
            ["route", "Ruta", "input", "text:short"],
            ["namespace", "Nombre ruta", "input", "text:short"],
            $this->select('parent_id', 'Relacion', 'menus')
        ];
        $empty = array_fill_keys(collect($inputs)->pluck(0)->toArray(), null);

        return [
            'inputs' => $this->array_inputs($inputs),
            'emptyValue' => $empty
        ];
    }

    public function getFormToEditWithEmptyValue()
    {
        $inputs = $this->getFormWithEmptyValue();

        return $inputs;
    }

    public function validate(array $data)
    {
        return Validator::make($data,self::rules())->validated();
    }

    public static function rules()
    {
        return [

            'id' => 'nullable',
            'name' => 'required',
            'icon' => 'nullable',
            'description' => 'nullable',
            'route' => 'nullable',
            'namespace' => 'required',
            'parent_id' => 'nullable'

        ];
    }
}
