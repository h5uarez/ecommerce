<?php

namespace Tests\Browser;

use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\DropdownCart;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Livewire\Livewire;
use Tests\DuskTestCase;

class ShoppingCartTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_product_is_added_to_the_cart_without_color_or_size()
    {
        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->assertEquals($product1->name, Cart::content()->first()->name);
    }

    /** @test */
    public function the_product_is_added_to_the_cart_with_color()
    {
        $product1 = $this->createProduct(true);

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->assertEquals($product1->name, Cart::content()->first()->name);
    }


    /** @test */
    public function the_product_is_added_to_the_cart_with_color_and_size()
    {
        $product1 = $this->createProduct(true, true);

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->assertEquals($product1->name, Cart::content()->first()->name);
    }

    /** @test */
    public function items_are_displayed_when_clicking_on_the_cart_icon()
    {
        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        Livewire::test(DropdownCart::class)->content();


    }
















    public function createProduct($color = false, $size = false)
    {
        $brand = Brand::factory()->create();

        $category = Category::factory()->create([
            'name' => 'Ropa',
        ]);

        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => $color,
            'size' => $size,
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        return $product;
    }







}
