<?php

namespace App;

use App\QueryFilter;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class ProductFilter extends QueryFilter
{
    public function rules(): array
    {
        return [
            'price' => '',
            'order' => ''
        ];
    }

    public function order($query, $dataOrder)
    {
        return $query->orderBy($dataOrder[0], $dataOrder[1]);
    }


    public function price($query, $range)
    {
        return $query->whereBetween('price', [$range[0], $range[1]]);
    }
}
