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
use App\Http\Livewire\CreateOrder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProductTest extends TestCase
{
    use DatabaseMigrations;



    /** @test */
    public function when_we_generate_the_order_its_decremented_in_the_db()
    {
        $product1 = $this->createProduct(false, false, 10);
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


    /** @test */
    public function check_the_expiration_of_pending_orders()
    {
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
