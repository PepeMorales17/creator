<?php

namespace Pp\Creator\Forms;

use Illuminate\Support\Facades\Validator;
use Pp\Creator\Traits\Inputs\InputTrait;


class CreateFolderForm
{
    use InputTrait;


    public function getFormWithEmptyValue()
    {
        $inputs = [
            ["name", "Nombre", "input", "text"],
            $this->select('parent_id', 'Raiz')
        ];
        $empty = array_fill_keys(collect($inputs)->pluck(0)->toArray(), null);
        //$this->empty()

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
        return Validator::make($data, self::rules())->validated();
    }

    public static function rules()
    {
        return [

            'id' => 'nullable',
            'name' => 'nullable',
            'parent_id' => 'nullable'

        ];
    }
}
