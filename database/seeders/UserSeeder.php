<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'admin']);

        User::factory()->create([
            'name' => 'Humberto Suárez',
            'email' => 'humberto@test.com',
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Maria Villascusa',
            'email' => 'maria@test.com',
        ]);

        User::factory()->create([
            'name' => 'Alejandro Martinez',
            'email' => 'alejandro@test.com',
        ]);

        User::factory(50)->create();
    }


}
