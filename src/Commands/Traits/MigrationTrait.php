<?php

namespace Pp\Creator\Commands\Traits;;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait MigrationTrait
{
    private function cols()
    {
        $class = $this->getCrudClass();
        $attr = collect($class->attrs())->map(function ($input) {
            return $this->createCol($input);
        });

        return $attr;
    }

    private function createCol($input)
    {
        $col = null;
        $hasCol = Arr::get($input, 'props.col');
        if (!!$hasCol) {
            $col = $hasCol;
        } else {
            $col = match ($input['type'] ?? null) {
                'id' => '$table->id()',
                'foreignId' => $this->foreignId($input),
                'string:short' => '$table->string(' . "'" . $input["id"] . "'" . ', 20)',
                'enum' => '$table->enum(' . "'" . $input["id"] . "'" . ', ' . str_replace(
                    '"',
                    "'",
                    str_replace(
                        '[',
                        'arropen',
                        str_replace(
                            ']',
                            'arrclose',
                            json_encode(Arr::get($input, 'props.enum'))
                        )
                    )
                ) . ')',
                default => $this->basicCol($input)
            };
        }

        if ($input['type'] != 'foreignId') {
            $col = $this->makeNull($col, $input);
        }
        $col = $this->endLine($col);
        return $col;
    }

    private function basicCol($input)
    {
        $col = Arr::get($input, 'props.col');
        if (!!$col) return $col;
        $col =  $this->baseCol($input);
        //$col = $this->makeNull($col, $input);
        return $col;
    }

    private function makeNull($col, $input)
    {
        if ($input['type'] === 'id') return $col;
        if (Arr::get($input, 'props.optional')) {
            $col .= '->nullable()';
        }
        return $col;
    }

    private function baseCol($input)
    {
        return '$table->' . $input['type'] . "('" . $input['id'] . "')";
    }

    private function foreignId($input)
    {
        $col = $this->baseCol($input);
        $col = $this->makeNull($col, $input);
        return $col . '->constrained()';
    }

    private function attrs()
    {
        $class = $this->getCrudClass();
        $attr = collect($class->attrs())->map(function ($input) {
            if (Arr::get($input, 'props.noMigration')) return null;
            return $this->createCol($input);
        })->filter();

        return $attr;
    }

    private function tablesBeforeMigrate()
    {
        $tables = collect($this->getCrudClass()->addTablesBeforeMigrate())->map(function ($item, $key) {
            $t = "Schema::create('{{name}}', function (Blueprint " . '$table' . ") {
                {{cols}}
            });";
            $t = str_replace('{{name}}', $key, $t);
            $cols = collect($item)->map(function ($input) {
                return $this->createCol($input);
            });
            $t = str_replace('{{cols}}', $this->resolveArray($cols->toArray()), $t);
            return $t;
        });

        return $tables->values()->toArray();
    }

    private function tablesAfterMigrate()
    {
        $crud = $this->getCrudClass();
        if (!method_exists($crud, 'addTablesAfterMigrate')) return [];
        $tables = collect($this->getCrudClass()->addTablesAfterMigrate())->map(function ($item, $key) {
            $t = "Schema::create('{{name}}', function (Blueprint " . '$table' . ") {
                {{cols}}
            });";
            $t = str_replace('{{name}}', $key, $t);
            $cols = collect($item)->map(function ($input) {
                return $this->createCol($input);
            });
            $t = str_replace('{{cols}}', $this->resolveArray($cols->toArray()), $t);
            return $t;
        });

        return $tables->values()->toArray();
    }

    private function tablesDown()
    {
        $crud = $this->getCrudClass();
        $tables = collect(array_merge(
            [$this->getTable()],
            collect($crud->addTablesBeforeMigrate())->keys()->toArray(),
            (method_exists($crud, 'addTablesAfterMigrate') ? collect($crud->addTablesAfterMigrate())->keys()->toArray() : [])
        ))->map(function ($item) {
            $t = "Schema::dropIfExists('{{name}}');";
            $t = str_replace('{{name}}', $item, $t);
            return $t;
        });

        return $tables->values()->toArray();
    }
}
