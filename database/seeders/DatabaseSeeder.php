<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User (Maissy)
        User::updateOrCreate(
            ['email' => 'maissy@gmail.com'],
            [
                'name' => 'Maissy Mayuni Safrida',
                'password' => Hash::make('maissy123'),
                'role' => 'user'
            ]
        );

        // Administrator Default
        User::updateOrCreate(
            ['email' => 'admin@simrpg.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]
        );
        $this->call(CountrySeeder::class);
        $this->call(LexiconSeeder::class);
        $this->call(PortSeeder::class);
    }
}