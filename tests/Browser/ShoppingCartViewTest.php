<?php

namespace Tests\Browser;

use App\Models\City;
use App\Models\Size;
use App\Models\User;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Image;
use Livewire\Livewire;
use App\Models\Product;
use Tests\DuskTestCase;
use App\Models\Category;
use App\Models\District;
use Laravel\Dusk\Browser;
use App\Models\Department;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ShoppingCartViewTest extends DuskTestCase
{
    use DatabaseMigrations;


    /** @test */
    public function shipping_option_is_selected()
    {
        $product1 = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->visit('/products/' . $product1->slug)
                ->click('@add-cart');
            $browser->loginAs(User::factory()->create());
            $browser->visit('/orders/create')
                ->check('@domicilio')
                ->assertVisible('@addressform')
                ->screenshot('shipping_option_is_selected');
        });
    }

    /** @test */
    public function shipping_option_is_not_selected()
    {
        $product1 = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->visit('/products/' . $product1->slug)
                ->click('@add-cart');
            $browser->loginAs(User::factory()->create());
            $browser->visit('/orders/create')
                ->assertMissing('@addressform')
                ->screenshot('shipping_option_is_not_selected');
        });
    }

    /** @test */
    public function the_order_is_created_the_cart_is_destroyed_and_redirected_to_the_new_route()
    {
        $product1 = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->visit('/products/' . $product1->slug)
                ->click('@add-cart');
            $browser->loginAs(User::factory()->create());
            $browser->visit('/orders/create')
                ->type('@contactname', 'Juan Cabalo Chiquito')
                ->type('@contactnumber', '667839485')
                ->click('@buybutton')
                ->pause(500)
                ->visit('/orders/' . $product1->id . '/payment')
                ->click('@dropdowncart')
                ->pause(500)
                ->assertVisible('@emptycart')
                ->screenshot('the_order_is_created_the_cart_is_destroyed_and_redirected_to_the_new_route');
        });
    }


    /** @test */
    public function selects_load_correctly()
    {
        $product1 = $this->createProduct();
        $department = Department::factory()->create();
        $city = City::factory()->create([
            'department_id' => $department->id
        ]);

        $district = District::factory()->create([
            'city_id' => $city->id
        ]);



        $this->browse(function (Browser $browser) use ($product1, $district) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product1->slug)
                ->click('@add-cart')
                ->pause(500)
                ->visit('/orders/create')
                ->check('@domicilio')
                ->click('@selectdepartment')
                ->pause(500)
                ->click('@optiondepartment')
                ->pause(500)
                ->click('@selectcity')
                ->pause(500)
                ->click('@optioncity')
                ->pause(500)
                ->click('@selectdistrict')
                ->pause(500)
                ->click('@optiondistrict')
                ->assertSee($district->name)
                ->screenshot('selects_load_correctly');
        });
    }


    /** @test */
    public function the_dropdown_menu_takes_us_to_orders_placed_by_us()
    {
        $product1 = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product1->slug)
                ->click('@add-cart')
                ->pause(500)
                ->click('@dropdowncart')
                ->pause(500)
                ->click('@shopping-cart')
                ->pause(500)
                ->click('@continue')
                ->pause(500)
                ->type('@contactname', 'Juan Cabalo Chiquito')
                ->type('@contactnumber', '667839485')
                ->click('@buybutton')
                ->pause(500)
                ->visit('/')
                ->click('@profile_image')
                ->pause(500)
                ->clickLink('Mis Pedidos')
                ->assertSee('Pedidos recientes')
                ->assertSee('PENDIENTE')
                ->assertRouteIs('orders.index')
                ->screenshot('the_dropdown_menu_takes_us_to_orders_placed_by_us');
        });
    }


    /** @test */
    public function the_stock_varies_when_adding_a_product_without_color_and_size()
    {
        $product1 = $this->createProduct(false, false, 50);

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product1->slug)
                ->click('@add-cart')
                ->pause(500)
                ->click('@add-cart')
                ->pause(500)
                ->click('@dropdowncart')
                ->pause(500)
                ->assertSee('Stock disponible: 48')
                ->screenshot('the_stock_varies_when_adding_a_product_without_color_and_size');
        });
    }

    /** @test */
    public function the_stock_varies_when_adding_a_product_with_color_without_size()
    {
        $product1 = $this->createProduct(true, false);
        $color = Color::create(['name' => 'Naranja']);

        $product1->colors()->attach([
            $color->id => [
                'quantity' => 10
            ]
        ]);

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product1->slug)
                ->pause(500)
                ->click('@color')
                ->pause(500)
                ->click('@colortype')
                ->pause(500)
                ->click('@add-color-item')
                ->pause(500)
                ->click('@add-color-item')
                ->pause(500)
                ->click('@dropdowncart')
                ->pause(500)
                ->assertSee('Stock disponible: 8')
                ->screenshot('the_stock_varies_when_adding_a_product_with_color_without_size');
        });
    }

    /** @test */
    public function the_stock_varies_when_adding_a_product_with_color_and_size()
    {
        $product1 = $this->createProduct(true, true);
        $color = Color::create(['name' => 'Naranja']);
        $size = Size::create([
            'name' => 'Talla XXXXL',
            'product_id' => $product1->id
        ]);

        $size->colors()->attach([
            1 => ['quantity' => 10],

        ]);

        $product1->colors()->attach([
            $color->id => [
                'quantity' => 10
            ]
        ]);

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->loginAs(User::factory()->create())
                ->visit('/products/' . $product1->slug)
                ->pause(500)
                ->click('@size')
                ->pause(500)
                ->click('@sizetype')
                ->pause(500)
                ->click('@color')
                ->pause(500)
                ->click('@colortype')
                ->pause(500)
                ->click('@add-full-item')
                ->pause(500)
                ->click('@dropdowncart')
                ->pause(500)
                ->assertSee('Stock disponible: 8')
                ->screenshot('the_stock_varies_when_adding_a_product_with_color_and_size');
        });
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
