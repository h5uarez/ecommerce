<?php

namespace Tests\Feature\Tareas;

use App\CreateData;
use Tests\TestCase;
use App\Models\User;
use App\Models\Brand;
use App\Models\Image;
use App\CreateProduct;
use Livewire\Livewire;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Http\Livewire\Search;
use App\Listeners\MergeTheCart;
use Illuminate\Auth\Events\Login;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\ShoppingCart;
use App\Http\Livewire\UpdateCartItem;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShoppingCartViewTest extends TestCase
{
    use DatabaseMigrations;
    use CreateProduct;
    use CreateData;

    //7- Al acceder a la vista del carrito, comprobar que podemos ver todos los items que tenga.

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


    //8- Comprobar que en dicha vista podemos cambiar la cantidad a cualquiera de ellos. Y la columna Total cambia consecuentemente.


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

    public function increment_and_decrement_product_with_color()
    {
        $product1 = $this->createProduct(true, false);

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
    public function increment_and_decrement_product_with_color_and_size()
    {
        $product1 = $this->createProduct(true, false);

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


    //11- Comprobar que el carrito se guarda en la BD cuando se cierra sesión y se recupera en caso de iniciar sesión y exista.
    //Pregunta 2 del exámen
    /** @test */
    public function the_cart_is_saved_when_you_log_out()
    {
        $product1 = $this->createData(false, false, 15);
        $product2 = $this->createData(false, false, 14);
        $product3 = $this->createData(false, false, 13);
        $product4 = $this->createData(false, false, 12);


        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);

        Livewire::test(AddCartItem::class, ['product' => $product3])
            ->call('addItem', $product3);

        $this->get('/orders/create')
            ->assertStatus(200)
            ->assertSee($product1->name)
            ->assertSee($product2->name)
            ->assertSee($product3->name)
            ->assertDontSee($product4->name);

        $price1 = $product1->price;
        $price2 = $product2->price;
        $price3 = $product3->price;

        $content = Cart::content();
        $this->post('logout');
        $this->assertDatabaseHas('shoppingcart', ['content' => serialize($content)]);

        $cback = new MergeTheCart();
        $relog = new Login('web', $user, true);
        $this->actingAs($user);

        $cback->handle($relog);

        $this->get('/orders/create')
            ->assertStatus(200)
            ->assertSee($product1->name)
            ->assertSee($price1)
            ->assertOk($product1->quantity = 15)
            ->assertSee($product2->name)
            ->assertSee($price2)
            ->assertOk($product2->quantity = 14)
            ->assertSee($product3->name)
            ->assertSee($price3)
            ->assertOk($product2->quantity = 13)
            ->assertDontSee($product4->name);
    }


    //6-Comprobar que el buscador es capaz de filtrar según la entrada de datos o mostrar todos si está vacío.

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
}
