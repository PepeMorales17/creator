<?php

namespace Pp\Creator\Traits\Inputs;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

/*
Este trait es para mantener las columas de mi base de datos con los mismos parametros
 */

trait InputTrait
{
    private $inputs = [
        "input" => "MyInput",
        "select" => "MySelect",
        "inputData" => "InputData",
        "from" => "MySelectFrom",
        "imodal" => "MyInputModal",
        "tmodal" => "TableModal",
        "text" => "MyTextarea",
        "table" => "InputTable",
        "fetch" => "InputFetch",
    ];

    private $inputProps = [
        "date" => ["type" => "date", 'format' => 'date'],
        "datetime" => ["type" => "datetime-local", 'format' => 'datetime', 'step' => "1"],
        "text" => ["type" => "text", 'format' => 'text'],
        "text:short" => ["type" => "text", 'format' => 'text', 'maxlength' => 20],
        "hidden" => ["type" => "hidden"],
        "file" => ["type" => "file", 'format' => 'file'],
        "bool" => ["data" => [['id' => 0, 'name' => 'No'], ['id' => 1, 'name' => 'Si']], "display" => "name"],
        "select" => [],
        "table" => [],
        "double" => ["type" => "number", "step" => "any", 'format' => 'number'],
        "currency" => ["type" => "number", "step" => "any", 'format' => 'currency'],
        "percentage" => ["type" => "number", "step" => "any", 'format' => 'percentage'],
        "number" => ["type" => "number", 'format' => 'number', "step" => "0"],
        "product" => ["url" => "admin.products.index", "table" => "product_view"],
        "postal" => ["url" => "postal.api", "values" => ['set' => ["city" => "city", "state" => "state"], 'fill' => ['colony_id' => 'colonies']]],
        "vendors" => ["url" => "admin.vendor.index", "table" => "vendor_view"],
        "companies" => ["url" => "admin.companies.index", "table" => "company_view"],
        "users" => ["url" => "admin.users.index", "table" => "users"],
        //"state" => ["data" => State::select("id", "name")->all(), "display" => "name"]
    ];

    protected function user($id = 'user_id')
    {
        return [$id, "Usuario", "select", ["data" => User::select("id", "name")->get()->keyBy("id"), "display" => "name"]];
    }

    protected function select($id, $label, $table = null, $keyBy = "id", $display = "name")
    {
        return [$id, $label, "select", [
            "data" =>
            match (true) {
                is_string($table) => DB::table($table)->select($keyBy, $display)->get()->keyBy($keyBy),
                $table instanceof Builder => $table->select($keyBy, $display)->get()->keyBy($keyBy),
                $table instanceof Collection => $table,
                is_array($table) => $table,
                $table === null => DB::table(Str::plural(str_replace('_id', '', $id)))->select($keyBy, $display)->get()->keyBy($keyBy),
                default => []
            }
            // $table ?
            // (is_string($table) ?
            // DB::table($table)->select($keyBy, $display)->get()->keyBy($keyBy)
            // : $table
            // )
            // : []
            , "display" => $display
        ]];
    }


    protected function editableTable($cols)
    {
        $completed_cols = [];
        foreach ($cols as $col) {
            $completed_cols[$col[0]] =  [
                "key" => $col[0],
                "header" => $col[1],
            ];

            $completed_cols[$col[0]] = $completed_cols[$col[0]] + $this->createInput($col);
            if ($completed_cols[$col[0]]['is'] === null) {
                isset($col[2]) ? $completed_cols[$col[0]]["formula"] = $col[2] : null;
                isset($col[3]) ? $completed_cols[$col[0]]["format"] = $col[3] : null;
            }
        }

        return $completed_cols;
    }

    protected function array_inputs($inputs)
    {
        $completed_inputs = [];
        // id, label, tipo, props, else
        foreach ($inputs as $key => $input) {
            if (!is_numeric($key)) {
                $completed_inputs[$key] = $input;
                continue;
            }
            $data = $this->createInput($input);
            array_push($completed_inputs, $data);
        }

        return $completed_inputs;
    }

    protected function createInput($input)
    {
        $data = [
            "id" => str_replace(".", "_", $input[0]),
            "key" => $input[0],
            "label" => $input[1],
            "is" => isset($input[2]) ? $this->inputs[$input[2]] ?? null : null,
        ];
        $extra = isset($input[3]) ? (is_array($input[3]) ? $input[3] : $this->inputProps[$input[3]]) : [];

        $data = array_merge($data, $extra);
        if (isset($input[4])) $data = array_merge($data, $input[4]);
        return $data;
    }

    protected function addId($form, $keys)
    {
        foreach ($keys as $key) {
            $form[$key] = array_merge(["id" => ["key" => "id", "header" => "id"]], $form[$key]);
        }

        return $form;
    }

    protected function addresses()
    {
        return ["addresses" => $this->editableTable($this->address())];
    }

    protected function morphMap()
    {
        return collect(Relation::morphMap())->map(function ($item, $key) {
            return [
                'id' => $key,
                'name' => $key
            ];
        })->toArray();
    }

    protected function address($withRefs = false)
    {
        $addr = [
            ["address_type_id", "Tipo", "select", ["data" => DB::table('address_types')->select("id", "name")->get()->keyBy("id"), "display" => "name"]],
            ["line1", "Calle", "input", "text"],
            ["line2", "Numero Exterior o manzana", "input", "text"],
            ["line3", "Interior, lote o edificio y depto", "input", "text"],
            ["postal_code_id", "Codigo Postal", "input", "postal"],
            ["colony_id", "Colonia", "select", ["data" => [], "display" => "name"]],
            ["other_colony", "Otra colonia no especificada", "input", "text"],
            ["city", "Ciudad", "input", "text", ["disabled" => true]],
            ["state", "Estado", "input", "text", ["disabled" => true]],
            // ["city_id", "Ciudad", "from", ["table" => "City", "listen" => "state_id", "display" => "name", "column" => "state_id"]],
            // ["state_id", "Estado", "select", ["data" => State::orderby('name', 'asc')->select("id", "name")->get()->keyBy("id"), "display" => "name"]],
        ];
        if ($withRefs) {
            $addr[] = ["btw_s1", "Entre calle 1", "input", "text"];
            $addr[] = ["btw_s2", "Entre calle 2", "input", "text"];
            $addr[] = ["refs", "Referencias", "input", "text"];
        }

        return $addr;
    }

    protected function bankAccount()
    {
        return  [
            ["owner", "Titular", "input", "text"],
            ["bank_id", "Banco", "select", ["data" => DB::table('banks')->select("id", "name")->get()->keyBy("id"), "display" => "name"]],
            ["account_number", "Numero de cuenta", "input", "text"],
            ["wire_transfer_number", "CLABE", "input", "text"],
            ["card_number", "Numero de tarjeta", "input", "text"]
        ];
    }

    protected function phones()
    {
        return ["phones" => $this->editableTable($this->phone())];
    }

    protected function phone()
    {
        return [
            ["phone", "Telefono", "input", "text"],
            ["phone_type_id", "Tipo", "select", ["data" => Db::table('phone_types')->select("id", "name")->get()->keyBy("id"), "display" => "name"]],
            //$this->note(),
        ];
    }

    protected function emails()
    {
        return ["emails" => $this->editableTable($this->email())];
    }
    protected function email()
    {
        return [
            ["email", "Correo Electronico", "input", "text"],
            ["email_type_id", "Tipo", "select", ["data" => DB::table('email_types')->select("id", "name")->get()->keyBy("id"), "display" => "name"]],
        ];
    }

    protected function date($id = "date", $label = "Fecha")
    {
        return [$id, $label, "input", "date"];
    }

    protected function note($rows = 1, $label = "Notas")
    {
        return ["note", $label, "text", "text", ["rows" => $rows]];
    }

    protected function taxInfo()
    {
        return [
            ['tax_name', 'Razon Social', 'input', 'text'],
            ['tax_id', 'RFC', 'input', 'text'],
            ['represented_by', 'Representado por', 'input', 'text'],
        ];
    }

    protected function quantity($label = "Cantidad")
    {
        return ["quantity", $label, "input", "double"];
    }
}
