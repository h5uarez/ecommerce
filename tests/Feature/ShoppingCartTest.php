<?php

namespace Tests\Feature;

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
use Tests\TestCase;

class ShoppingCartTest extends TestCase
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
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        Livewire::test(DropdownCart::class)
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);
    }

    /** @test */
    public function when_adding_an_item_to_the_cart_the_number_in_the_red_circuit_increases()
    {
        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->assertEquals(Cart::count(), 1);
    }

    /** @test */
    public function it_is_not_possible_to_add_more_quantity_of_a_product_than_the_stock_it_has_to_the_cart()
    {
        $product1 = $this->createProduct(false, false, 2);

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1)
            ->call('addItem', $product1)
            ->call('addItem', $product1);

        Livewire::test(DropdownCart::class)
            ->assertCount($product1->quantity);

        //SIn terminar
    }


    public function createProduct($color = false, $size = false, $quantity = 15)
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
            'quantity' => $quantity,
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        return $product;

        //Sin terminar
    }
}
