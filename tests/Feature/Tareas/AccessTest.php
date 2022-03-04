<?php

namespace Tests\Feature\Tareas;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\CreateProduct;
use Livewire\Livewire;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\CreateOrder;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AccessTest extends TestCase
{
    use DatabaseMigrations;

    use CreateProduct;


    //10- Comprobar que solo un usuario autenticado puede entrar a crear un pedido.

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


    //1-Verificar que no podemos acceder sin autenticar a las rutas en las que debemos estarlo. Y que si podemos entrar si lo estamos.

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
        Role::create(['name' => 'admin']);

        $this->actingAs(User::factory()->create()->assignRole('admin'))
            ->get('/admin')
            ->assertStatus(200);
    }


    //2- Verificar la politica creada.

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
}
