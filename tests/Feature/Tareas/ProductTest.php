<?php

namespace Tests\Feature\Tareas;

use App\CreateData;
use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\CreateProduct;
use Livewire\Livewire;
use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\CreateOrder;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductTest extends TestCase
{
    use DatabaseMigrations;

    use CreateProduct;

    use CreateData;


    //5- Comprobar que al generar el pedido, el stock cambia en la BD
    /** @test */
    public function when_we_generate_the_order_its_decremented_in_the_db()
    {
        $product1 = $this->createData(false, false, 10);
        $this->actingAs(User::factory()->create());

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);


        Livewire::test(CreateOrder::class)
            ->set('contact', 'Juan Cabalo')
            ->set('phone', '678728394')
            ->call('create_order');

        $this->assertDatabaseHas('products', [
            'quantity' => 9
        ]);
    }


    //6- Verificar la caducidad de los pedidos pendientes.
    /** @test */
    public function check_the_expiration_of_pending_orders()
    {

        $product1 = $this->createProduct();
        $this->actingAs(User::factory()->create());

        Livewire::test(AddCartItem::class, ['product' => $product1])
            ->call('addItem', $product1);

        Livewire::test(CreateOrder::class)
            ->set('contact', 'Juan Cabalo')
            ->set('phone', '678728394')
            ->call('create_order');


        $order = Order::first();
        $order->created_at = now()->subMinute(15);
        $order->save();

        $this->artisan('schedule:run');
        $order = Order::first();
        $this->assertEquals($order->status, 5);
    }
}
