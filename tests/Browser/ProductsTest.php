<?php

namespace Tests\Browser;

use App\CreateProduct;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductsTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateProduct;

    //2- Comprobar que en la vista principal podemos ver al menos 5 productos de una categoría.

    /** @test */
    public function main_view_we_can_see_at_least_five_products_of_a_category()
    {
        $categories = Category::factory()->create();
        $brand = Brand::factory()->create();
        $categories->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $categories->id,
            'name' => 'Ejemplo'
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $product3 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product3->id,
            'imageable_type' => Product::class
        ]);

        $product4 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product4->id,
            'imageable_type' => Product::class
        ]);

        $product5 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory()->create([
            'imageable_id' => $product5->id,
            'imageable_type' => Product::class
        ]);

        $product6 = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product1, $product2, $product3, $product4, $product5, $product6, $subcategory) {
            $browser->visit('/')
                ->clickLink('Categorías')
                ->pause(500)
                ->clickLink('Ejemplo')
                ->assertSee(substr($product1->name, 0, 9))
                ->assertSee(substr($product2->name, 0, 9))
                ->assertSee(substr($product3->name, 0, 9))
                ->assertSee(substr($product4->name, 0, 9))
                ->assertSee(substr($product5->name, 0, 9))
                ->assertDontSee(substr($product6->name, 0, 9))
                ->screenshot('five_products_main_view');
        });
    }



    //3- Igual que el anterior pero comprobando que los productos están publicados (no podemos ver los no publicados)

    /** @test */
    public function we_cannot_see_unpublished_products()
    {
        $categories = Category::factory()->create();
        $brand = Brand::factory()->create();
        $categories->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $categories->id
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $product3 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'status' => '1'
        ]);

        Image::factory()->create([
            'imageable_id' => $product3->id,
            'imageable_type' => Product::class
        ]);

        $product4 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'status' => '1'
        ]);

        Image::factory()->create([
            'imageable_id' => $product4->id,
            'imageable_type' => Product::class
        ]);

        $product5 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'status' => '1'
        ]);

        Image::factory()->create([
            'imageable_id' => $product5->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1, $product2, $product3, $product4, $product5) {
            $browser->visit('/')
                ->assertSee(substr($product1->name, 0, 9))
                ->assertSee(substr($product2->name, 0, 9))
                ->assertDontSee(substr($product3->name, 0, 9))
                ->assertDontSee(substr($product4->name, 0, 9))
                ->assertDontSee(substr($product5->name, 0, 9))
                ->screenshot('unpublished_products');
        });
    }

    //4- Verificar que podemos acceder a la vista de detalle de una categoría. Y allí somos capaces de ver lo previsto. (Subcategorías, marcas y productos).

    /** @test */
    public function we_can_access_the_detail_view_of_a_category_and_see_its_stuff()
    {
        $categories = Category::factory()->create();
        $brand = Brand::factory()->create();
        $categories->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $categories->id
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1, $categories, $subcategory, $brand) {
            $browser->visit('/categories/' . $categories->slug)
                ->assertSee(substr($product1->name, 0, 9))
                ->assertSee($subcategory->name)
                ->screenshot('detail_view_category');
        });
    }


    //5- Verificar que al pinchar en el menú de la izq. (en la vista de detalle de una categoría) filtra los productos por subcategoría o por marca.

    /** @test */
    public function filter_products_by_subcategory_or_by_brand()
    {
        $categories = Category::factory()->create();

        $brand1 = Brand::factory()->create([
            'name' => 'Puma'
        ]);
        $brand2 = Brand::factory()->create([
            'name' => 'Nike'
        ]);

        $categories->brands()->attach($brand1->id);

        $categories->brands()->attach($brand2->id);


        $subcategory1 = Subcategory::factory()->create([
            'name' => 'Pantalones',
            'category_id' => $categories->id
        ]);

        $subcategory2 = Subcategory::factory()->create([
            'name' => 'Camisetas',
            'category_id' => $categories->id
        ]);

        $product1 = Product::factory()->create([
            'name' => 'Pantalones nike',
            'subcategory_id' => $subcategory1->id,
            'brand_id' => $brand2->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Camiseta Puma',
            'subcategory_id' => $subcategory2->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $product3 = Product::factory()->create([
            'name' => 'Pantalones puma',
            'subcategory_id' => $subcategory1->id,
            'brand_id' => $brand1->id
        ]);

        Image::factory()->create([
            'imageable_id' => $product3->id,
            'imageable_type' => Product::class
        ]);

        $product4 = $this->createProduct();


        $this->browse(function (Browser $browser) use ($categories, $product1, $product3, $product4, $subcategory1, $subcategory2) {
            $browser->visit('/categories/' . $categories->slug)
                ->clickLink('Pantalones')
                ->pause(500)
                ->assertSee($subcategory1->name)
                ->assertSee($subcategory2->name)
                ->assertSee($product1->name)
                ->assertSee($product3->name)
                ->assertDontSee($product4->name)
                ->screenshot('filter_products_by_subcategory_or_brand');
        });
    }


    //6- Verificar que podemos acceder a la vista de detalle de un producto.
    //7- En la vista de detalle deben verse las imágenes, la descripción del producto, el nombre, precio y el stock, con los botones para cambiar la cantidad y agregar al carrito.

    /** @test */
    public function we_can_access_the_detail_view_of_a_product()
    {
        $product1 = $this->createProduct();

        $this->browse(function (Browser $browser) use ($product1) {
            $browser->visit('/products/' . $product1->slug)
                ->pause(500)
                ->assertSee($product1->name)
                ->assertSee('Stock disponible: ' . $product1->quantity)
                ->assertSee($product1->price)
                ->assertVisible('@add-cart')
                ->assertVisible('@button_more')
                ->assertVisible('@button_less')
                ->pause(500)
                ->screenshot('detail_view_of_product');
        });
    }

    //8- Comprobar los límites de los botones + y - en la vista detalle del producto

    /** @test */
    public function product_button_limits()
    {
        $categories = Category::factory()->create();
        $brand = Brand::factory()->create();
        $categories->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $categories->id,
            'color' => '0',
            'size' => '0'
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => '3',

        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1, $categories) {
            $browser->visit('/products/' . $product1->slug)
                ->click('@button_more')
                ->click('@button_more')
                ->click('@button_more')
                ->pause(500)
                ->assertButtonDisabled('@button_more')
                ->pause(500)
                ->screenshot('buttons_of_product');
        });
    }

    //9 - Comprobar que vemos los desplegables de talla y color según el producto elegido.

    /** @test */
    public function we_do_not_see_the_size_and_color_drop_downs_according_to_the_chosen_product()
    {
        $categories = Category::factory()->create();
        $brand = Brand::factory()->create();
        $categories->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $categories->id,
            'color' => '0',
            'size' => '0'
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => '3',

        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1, $categories) {
            $browser->visit('/products/' . $product1->slug)
                ->pause(500)
                ->assertMissing('@color')
                ->assertMissing('@size')
                ->screenshot('we_not_see_size_and_color');
        });
    }

    /** @test */
    public function we_see_the_color_drop_downs_according_to_the_chosen_product()
    {
        $categories = Category::factory()->create();
        $brand = Brand::factory()->create();
        $categories->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $categories->id,
            'color' => '1',
            'size' => '0'
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => '3',

        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1, $categories) {
            $browser->visit('/products/' . $product1->slug)
                ->pause(500)
                ->assertVisible('@color')
                ->assertMissing('@size')
                ->screenshot('we_see_color_of_product');
        });
    }

    /** @test */

    public function we_see_the_size_and_color_drop_downs_according_to_the_chosen_product()
    {
        $categories = Category::factory()->create();
        $brand = Brand::factory()->create();
        $categories->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $categories->id,
            'color' => '1',
            'size' => '1'
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => '3',

        ]);

        Image::factory()->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1, $categories) {
            $browser->visit('/products/' . $product1->slug)
                ->pause(500)
                ->assertVisible('@size')
                ->assertVisible('@color')
                ->screenshot('we_see_size_and_color');
        });
    }
}
