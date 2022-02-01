<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SubcategoryTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function see_category()
    {
        $categories = Category::factory()->create();
        $this->browse(function (Browser $browser) use ($categories) {
            $browser->visit('/')
                ->clickLink('Categorías')
                ->assertSee($categories->name)
                ->screenshot('see_category-test');
        });
    }

    /** @test */
    public function see_subcategory()
    {
        $category1 = Category::factory()->create([
            'name' => 'Celulares y tablets'
        ]);

        $sub1 = Subcategory::factory()->create([
            'category_id' => $category1->id,
            'name' => 'Celulares y smartphones'
        ]);

        $this->browse(function (Browser $browser) use ($category1, $sub1) {
            $browser->visit('/')
                ->clickLink('Categorías')
                ->assertSee($category1->name)
                ->mouseover('@category')
                ->assertSee('Celulares y smartphones')
                ->screenshot('see_subcategory-test');
        });

    }
}
