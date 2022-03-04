<?php

namespace App\Http\Livewire\Admin;

use App\Models\Size;
use App\Models\Brand;
use App\Models\Color;
use App\Filters\ProductFilter;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Models\Subcategory;
use Livewire\WithPagination;


class ShowProducts2 extends Component
{
    use WithPagination;

    public $search;
    public $pagination = 15;
    public $columns = ['Categoría', 'Estado', 'Precio', 'Marca', 'Stock', 'Colores', 'Tallas', 'Fecha de creación', 'Fecha de edición'];
    public $selectedColumns = [];
    public $icon = '-circle';
    public $maxPrice = 80000;
    public $minPrice = 0;
    public $orderColumn = 'name';
    public $orderDirection = 'asc';

    public function sortCol($orderColumn, $orderDirection)
    {
        $this->orderColumn = $orderColumn;
        $this->orderDirection = $orderDirection;
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function getProducts(ProductFilter $productFilter)
    {
        $priceMaxMin = [$this->minPrice, $this->maxPrice];
        $dataOrder = [$this->orderColumn, $this->orderDirection];

        $products = Product::query()
            ->filterBy($productFilter, [
                'price' => $priceMaxMin,
                'order' => $dataOrder,
            ])->paginate($this->pagination);

        return $products;
    }

    public function updatingPagination()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedColumns = $this->columns;
    }

    public function showColumn($column)
    {
        return in_array($column, $this->selectedColumns);
    }

    public function render(ProductFilter $productFilter)
    {

        $products = Product::query()->where('name', 'LIKE', "%{$this->search}%");

        $products = $products->paginate($this->pagination);

        return view('livewire.admin.show-products2', [
            'products' => $this->getProducts($productFilter),
            'orderDirection' => $this->orderDirection,
        ])->layout('layouts.admin');
    }
}
