<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Subcategory;
use Database\Factories\SubcategoryFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function see_category()
    {
        $categories= Category::factory()->create();
        $this->browse(function (Browser $browser) use ($categories){
            $browser->visit('/')
                ->clickLink('Categorías')
                ->assertSee($categories->name)
                ->screenshot('see_category-test');
        });
    }

    /** @test */
    /*public function see_subcategory()
    {
        $category1 = Category::factory()->create([
            'name' => 'Celulares y tablets'
        ]);

        $sub1 = Subcategory::factory()->create([
            'category_id' => $category1->id
        ]);

        dd($sub1);

        $this->browse(function (Browser $browser) use ($category1){
            $browser->visit('/')
                ->clickLink('Categorías')
                ->assertSee('Celulares y tablets')
                ->screenshot('see_subcategory-test');
        });

    }*/
}
