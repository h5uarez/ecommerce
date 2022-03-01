<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Brand;
use App\Models\Image;
use Livewire\Livewire;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Http\Livewire\AddCartItem;
use Spatie\Permission\Models\Role;
use App\Http\Livewire\CreateOrder;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function we_cannot_access_unauthenticated_routes_where_we_should_be()
    {
        $this->get('/admin')
            ->assertStatus(302)
            ->assertRedirect('/login');


        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        $this->get('/orders/create')
            ->assertStatus(302)
            ->assertRedirect('/login');
    }


    /** @test */
    public function we_cannot_access_admin_routes_without_being_admin()
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertStatus(403);
    }

    /** @test */
    public function we_can_access_admin_routes_with_admin_user()
    {
        $role = Role::create(['name' => 'admin']);

        $this->actingAs(User::factory()->create()->assignRole('admin'))
            ->get('/admin')
            ->assertStatus(200);
    }


    /** @test */
    public function we_cannot_access_to_shopping_cart_if_you_dont_create_that()
    {
        $this->actingAs(User::factory()->create(['id' => '1']));

        $product1 = $this->createProduct();

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'Humberto Suárez')
            ->set('phone', '671349283')
            ->call('create_order');


        $this->actingAs(User::factory()->create(['id' => '2']))
            ->get('/orders/1/payment')
            ->assertStatus(403);
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
