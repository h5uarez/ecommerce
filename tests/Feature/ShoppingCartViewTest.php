<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Brand;
use App\Models\Image;
use Livewire\Livewire;
use App\Http\Livewire\Search;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\ShoppingCart;
use App\Http\Livewire\UpdateCartItem;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShoppingCartViewTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function we_can_see_all_the_elements_that_the_view_has()
    {
        $product0 = $this->createProduct();
        $product1 = $this->createProduct();
        $product2 = $this->createProduct(true);
        $product3 = $this->createProduct(true, true);


        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);
        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);
        Livewire::test(AddCartItem::class, ['product' => $product3])
            ->call('addItem', $product3);

        $this->get('/shopping-cart')
            ->assertStatus(200)
            ->assertSee($product1->name)
            ->assertSee($product2->name)
            ->assertSee($product3->name)
            ->assertDontSee($product0->name);
    }

    /** @test */

    public function increment_and_decrement_product_without_color_or_size()
    {
        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $total = Cart::subtotal();

        Livewire::test(UpdateCartItem::class, ['rowId' => Cart::content()->first()->rowId])
            ->call('increment')
            ->call('increment');
        $this->assertEquals($total * 3, Cart::subtotal());

        Livewire::test(UpdateCartItem::class, ['rowId' => Cart::content()->first()->rowId])
            ->call('decrement')
            ->call('decrement');
        $this->assertEquals($total, Cart::subtotal());
    }

    /** @test */
    public function not_logged_users_cant_access_to_create_an_order()
    {
        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->get('/orders/create')
            ->assertStatus(302)
            ->assertRedirect('/login');
    }

    /** @test */
    public function logged_users_can_access_to_create_an_order()
    {
        $product1 = $this->createProduct();
        $user = User::factory()->create();
        $this->actingAs($user);


        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->get('/orders/create')
            ->assertStatus(200);
    }


    /** @test */
    public function the_cart_is_saved_when_you_log_out()
    {
        $product1 = $this->createProduct();
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->get('/orders/create')
            ->assertStatus(200)
            ->assertSee($product1->name);
            
        $content = Cart::content();
        $this->post('logout');
        $this->actingAs($user);
        $this->assertDatabaseHas('shoppingcart', ['content' => serialize($content)]);
    }

    /** @test */

    public function filter_search_with_name()
    {
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(Search::class)
            ->set('search', $product1->name)
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);
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
    }
}
