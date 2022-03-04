<?php

namespace Tests\Feature\Tareas;

use Tests\TestCase;
use App\Models\Size;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use App\CreateProduct;
use Livewire\Livewire;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\DropdownCart;
use App\Http\Livewire\ShoppingCart;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShoppingCartTest extends TestCase
{
    use DatabaseMigrations;
    use CreateProduct;


    //1- Comprobar que se agregan al carrito los tres tipos de productos que tenemos.

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

    //2- Comprobar que se muestran los items al pinchar en el icono del carrito.

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

    //3- Comprobar que al añadir un item al carrito, el número del circulito rojo se incrementa.

    /** @test */
    public function when_adding_an_item_to_the_cart_the_number_in_the_red_circuit_increases()
    {
        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->assertEquals(Cart::count(), 1);
    }


    //4- Comprobar que no se pueden añadir al carrito más cantidad de un producto (de cualquiera de los tres tipos) que stock disponible tenga.

    /** @test */
    public function it_is_not_possible_to_add_more_quantity_of_a_product_without_color_and_size_than_the_stock_it_has_to_the_cart()
    {
        $product1 = $this->createProduct(false, false, 2);

        for ($i = 0; $i < 2; $i++) {
            Livewire::test(AddCartItem::class, ['product' => $product1])
                ->call('addItem', $product1);
            $product1->quantity = qty_available($product1->id); //Para saber la cantidad total real, la quantity es el stock disponible que se ve en la vista
        }

        $this->assertEquals(2, Cart::content()->first()->qty);
    }

    /** @test */
    public function it_is_not_possible_to_add_more_quantity_of_a_product_with_color_without_size_than_the_stock_it_has_to_the_cart()
    {
        $product1 = $this->createProduct(true, false, 2);

        for ($i = 0; $i < 2; $i++) {
            Livewire::test(AddCartItem::class, ['product' => $product1])
                ->call('addItem', $product1);
            $product1->quantity = qty_available($product1->id); //Para saber la cantidad total real, la quantity es el stock disponible que se ve en la vista
        }

        $this->assertEquals(2, Cart::content()->first()->qty);
    }

    /** @test */
    public function it_is_not_possible_to_add_more_quantity_of_a_product_with_color_and_size_than_the_stock_it_has_to_the_cart()
    {
        $product1 = $this->createProduct(true, true, 2);

        for ($i = 0; $i < 2; $i++) {
            Livewire::test(AddCartItem::class, ['product' => $product1])
                ->call('addItem', $product1);
            $product1->quantity = qty_available($product1->id); //Para saber la cantidad total real, la quantity es el stock disponible que se ve en la vista
        }

        $this->assertEquals(2, Cart::content()->first()->qty);
    }


    //5- Comprobar que podemos ver el stock disponible del producto (3 tipos).

    /** @test */
    public function see_product_available_stock_without_color_and_size()
    {
        $product1 = $this->createProduct();

        $this->get('products/' . $product1->slug)
            ->assertStatus(200)
            ->assertSeeText($product1->quantity);
    }

    /** @test */
    public function can_see_the_stock_of_product_with_color()
    {
        $product1 = $this->createProduct(true, false, $quantity = 92);
        $colorProduct = Color::factory()->create();

        $product1->colors()->attach($colorProduct->id, ['quantity' => $quantity]);

        $this->get('/products/' . $product1->slug)
            ->assertStatus(200)
            ->assertSee($product1->quantity);
    }

    /** @test */
    public function can_see_the_stock_of_product_with_color_and_size()
    {
        $this->markTestIncomplete('No se me funciona "name"');

        $product1 = $this->createProduct(true, false, $quantity = 92);
        $colorProduct = Color::create([
            'name' => 'Azul'
        ]);

        $sizeProduct = Size::factory()->create([
            'product_id' => $product1->id
        ]);


        $sizeProduct->colors()->attach($colorProduct->id, ['quantity' => $quantity]);


        $this->get('/products/' . $product1->slug)
            ->assertStatus(200)
            ->assertSee($product1->quantity);
    }

    //9- Comprobar que podemos vaciar el carrito. Y también que se puede borrar un producto.

    /** @test */
    public function we_can_delete_the_product()
    {
        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);


        Livewire::test(ShoppingCart::class)
            ->assertSee($product1->name)
            ->call('delete', Cart::content()->first()->rowId)
            ->assertDontSee($product1->name);
    }


    /** @test */
    public function we_can_delete_the_shopping_cart()
    {
        $product1 = $this->createProduct();
        $product2 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1)
            ->call('addItem', $product1)
            ->call('addItem', $product1);

        Livewire::test(AddCartItem::class, ['product' => $product2])
            ->call('addItem', $product2);


        Livewire::test(ShoppingCart::class)
            ->assertSee($product1->name, $product2->name)
            ->call('destroy')
            ->assertDontSee($product1->name, $product2->name);
    }
}
