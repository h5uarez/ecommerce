<?php

namespace App\Filters;

use App\Filters\QueryFilter;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

//Examen
class ProductFilter extends QueryFilter
{
    public function rules(): array
    {
        return [
            'price' => '',
            'order' => '',
            'from' => 'date_format:d/m/Y',
            'to' => 'date_format:d/m/Y',
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

    public function from($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);

        $query->whereDate('created_at', '>=', $date);
    }

    public function to($query, $date)
    {
        $date = Carbon::createFromFormat('d/m/Y', $date);

        $query->whereDate('created_at', '<=', $date);
    }
}
