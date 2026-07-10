<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(

            [
                'email' => 'maissy@gmail.com'
            ],

            [
                'name' => 'Maissy Mayuni Safrida',

                'password' => Hash::make('maissy123')

            ]

        );
    }
}