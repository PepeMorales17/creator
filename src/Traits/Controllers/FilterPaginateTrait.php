<?php

namespace Pp\Creator\Traits\Controllers;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait FilterPaginateTrait {


    protected function filteredModel($model, $table = null)
    {
        $search = request()->term;
        $table = match(true) {
            is_string($table) => $table,
            $model instanceof Model => $model->getTable(),
            $model instanceof Builder => $model->getModel()->getTable()

        };

        return $model->when($search, function ($query, $term) use($table) {
            $query->where(DB::raw($this->concatCols($table)), "LIKE", "%" . $term . "%");
        });;
    }

    private function concatCols($table)
    {
        return 'CONCAT('.collect(Schema::getColumnListing($table))->map(function($item) use($table) {
            return 'IFNULL('.$table.'.'.$item.',""), "◬"';
        })->implode(', ').')';
    }

    public function index()
    {
        $toPaginate = $this->toPaginate();
        if (is_array($toPaginate)) return $this->justRenderIndex($this->toPaginate()[0]->get());
        if (request()->wantsJson()) return $this->filteredModel($this->toPaginate())->simplePaginate(30);
        return $this->justRenderIndex($this->toPaginate()->simplePaginate(30));
    }

    //public abstract function toPaginate();

}
