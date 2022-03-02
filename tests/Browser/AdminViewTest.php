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
        $product1 = $this->createProduct('Zapatillas Jordan');
        $product2 = $this->createProduct('Pantalones Levis');



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


    /** @test */
    public function check_product_creation_validation_include()
    {
        Role::create(['name' => 'admin']);

        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::factory()->create()->assignRole('admin'))
                ->pause(500)
                ->visit('/admin')
                ->pause(500)
                ->type('@adminsearch', 'Zap')
                ->pause(500)
                ->screenshot('check_admin_product_finder');
        });
    }













    public function createProduct($name)
    {
        $brand = Brand::factory()->create();

        $category = Category::factory()->create([]);

        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false,
        ]);

        $product = Product::factory()->create([
            'name' => $name,
            'subcategory_id' => $subcategory->id,
            'quantity' => 15,
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class,
        ]);

        return $product;
    }
}
