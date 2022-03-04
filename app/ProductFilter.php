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
        ];
    }


    public function price($query, $range)
    {
        return $query->whereBetween('price', [$range[0], $range[1]]);
    }
}
