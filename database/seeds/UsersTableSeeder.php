<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'administrador@gmail.com')->first();
        if (!$user) {
            User::firstOrCreate([
                'name' => 'Administrador',
                'email' => 'administrador@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'remember_token' => Str::random(10),
            ]);
        }

        factory(User::class, 1000)->create();
    }
}
