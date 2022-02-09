<?php

namespace Tests\Browser;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;


class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $categories = Category::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->pause(500)
                ->assertSee('Categorías')
                ->screenshot('example-test');
        });
    }
}
