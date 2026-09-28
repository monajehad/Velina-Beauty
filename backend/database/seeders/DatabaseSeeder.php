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

        // Additional admin: nader / 1234
        User::firstOrCreate(
            ['email' => 'nader'],
            [
                'name' => 'Nader',
                'password' => Hash::make('1234'),
                'is_admin' => true,
            ]
        );

        // Additional admin: moner / 1234
        User::firstOrCreate(
            ['email' => 'moner'],
            [
                'name' => 'Moner',
                'password' => Hash::make('1234'),
                'is_admin' => true,
            ]
        );

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
