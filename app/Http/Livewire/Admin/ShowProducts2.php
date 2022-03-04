<?php

namespace App\Http\Livewire\Admin;

use App\Models\Size;
use App\Models\Brand;
use App\Models\Color;
use App\ProductFilter;
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
    public $camp = null;
    public $order = null;
    public $icon = '-circle';
    public $maxPrice = 80000;
    public $minPrice = 0;



    public function sortable($camp)
    {
        if ($camp !== $this->camp) {
            $this->order = null;
        }
        switch ($this->order) {
            case null:
                $this->order = 'asc';
                $this->icon = '-arrow-circle-up';
                break;
            case 'asc':
                $this->order = 'desc';
                $this->icon = '-arrow-circle-down';
                break;
            case 'desc':
                $this->order = null;
                $this->icon = '-circle';
                break;
        }

        $this->camp = $camp;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function getProducts(ProductFilter $productFilter)
    {
        $priceMaxMin = [$this->minPrice, $this->maxPrice];

        $products = Product::query()
            ->filterBy($productFilter, [
                'price' => $priceMaxMin,
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


        if ($this->camp && $this->order) {
            $products = $products->orderBy($this->camp, $this->order);
        }

        $products = $products->paginate($this->pagination);



        return view('livewire.admin.show-products2', [
            'products' => $this->getProducts($productFilter),
        ])->layout('layouts.admin');
    }
}
