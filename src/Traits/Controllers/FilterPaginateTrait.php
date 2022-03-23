<?php

namespace Pp\Creator\Traits\Controllers;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Str;


trait FilterPaginateTrait
{

    public $availableFilters = null;

    protected function filteredModel($model, $table = null)
    {
        $search = request()->term;

        $table = match (true) {
            is_string($table) => $table,
            $model instanceof Model => $model->getTable(),
            $model instanceof Builder => $model->getModel()->getTable()
        };

        $query = $model->when($search, function ($query, $term) use ($table) {
            $query->where(DB::raw($this->concatCols($table)), "LIKE", "%" . strtolower($term) . "%");
        });

        $filterClass = $this->getFilterClass();
        if (class_exists($filterClass)) {
            $advancedFilter = collect(json_decode(request()->advancedFilter, true));

            $query->when($advancedFilter->count(), function ($query) use ($advancedFilter, $filterClass) {
                $this->availableFilters = collect((new $filterClass())->getFilters());

                $advancedFilter->map(function ($value, $key) use ($query) {
                    $fp = $this->availableFilters[$key]; //filterProps
                    match ($fp['type']) {
                        'where' => $query->when($value, fn ($qt) => $qt->where($fp['col'], $fp['whereType'], $value)),
                        'date' =>  $query->when($value, fn ($qt) => $qt->whereRaw("DATE_FORMAT(`date`, '" . $fp['raw'] . "') = '$value'")),
                    };
                });
            });
        }
        return $query;
    }

    private function concatCols($table)
    {
        return 'CONCAT(' . collect(Schema::getColumnListing($table))->map(function ($item) use ($table) {
            return 'LOWER(IFNULL(' . $table . '.' . $item . ',"")), "◬"';
        })->implode(', ') . ')';
    }

    public function index()
    {
        $toPaginate = $this->toPaginate();
        if (is_array($toPaginate)) return $this->justRenderIndex($this->toPaginate()[0]->get());
        if (request()->wantsJson()) return $this->filteredModel($this->toPaginate())->simplePaginate(30);
        return $this->justRenderIndex($this->toPaginate()->simplePaginate(30));
    }

    public function getFilterClass()
    {
        $route = request()->route()->getName();
        $routeStudly = Str::studly(str_replace(".", "_", $route));
        return 'App\Filters\\' . $routeStudly . 'Filters';
    }

    //public abstract function toPaginate();

}
