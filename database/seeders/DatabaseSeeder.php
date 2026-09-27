<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account — change the password after first login.
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@velina.com')],
            [
                'name' => 'Nader Munir Mahmoud',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'VelinaAdmin123')),
                'is_admin' => true,
            ]
        );

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
