<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Brand;
use App\Models\Image;
use App\Models\Product;
use Tests\DuskTestCase;
use App\Models\Category;
use Laravel\Dusk\Browser;
use App\Models\Subcategory;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseMigrations;



class AdminViewTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function check_admin_product_finder()
    {
        $brand = Brand::factory()->create();

        $category = Category::factory()->create([
            'name' => 'Ropa',
        ]);

        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
        ]);

        $product1 = Product::factory()->create([
            'name' => 'Zapatillas ProBounce',
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class,
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Camiseta Adidas',
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory()->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class,
        ]);


        Role::create(['name' => 'admin']);

        $this->browse(function (Browser $browser) use ($product1, $product2) {
            $browser->loginAs(User::factory()->create()->assignRole('admin'))
                ->pause(500)
                ->visit('/admin')
                ->pause(500)
                ->type('@adminsearch', 'Zap')
                ->pause(500)
                ->assertSee($product1->name)
                ->assertDontSee($product2->name)
                ->screenshot('check_admin_product_finder');
        });
    }
}
