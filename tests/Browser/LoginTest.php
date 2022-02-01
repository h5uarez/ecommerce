<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function login_and_registration_links_without_logging_in()
    {
        $categories = Category::factory()->create();
        $this->browse(function (Browser $browser)  {
            $browser->visit('/')
                ->click('@not_logged')
                ->pause(500)
                ->assertSee('Iniciar sesión')
                ->assertSee('Registrarse')
                ->screenshot('see_mainView-test');

        });
    }

    /** @test */
    public function profile_and_logout_links_logged()
    {
        $categories = Category::factory()->create();
        $user = User::factory()->create();
        $this->browse(function (Browser $browser) use ($user){
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->pause(500)
                ->click('@loggin_button')
                ->click('@loggin_img')
                ->pause(500)
                ->assertSee('Perfil')
                ->assertSee('Finalizar sesión')
                ->screenshot('see_loginView');

        });

    }
}
