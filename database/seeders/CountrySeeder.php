<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::insert([

            [
                'country_name' => 'Indonesia',
                'country_code' => 'ID',
                'capital' => 'Jakarta',
                'currency' => 'IDR',
                'risk_score' => 45,
                'risk_level' => 'Medium',
                'temperature' => 29,
                'weather' => 'Cloudy',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'China',
                'country_code' => 'CN',
                'capital' => 'Beijing',
                'currency' => 'CNY',
                'risk_score' => 82,
                'risk_level' => 'High',
                'temperature' => 27,
                'weather' => 'Sunny',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'Russia',
                'country_code' => 'RU',
                'capital' => 'Moscow',
                'currency' => 'RUB',
                'risk_score' => 76,
                'risk_level' => 'High',
                'temperature' => 18,
                'weather' => 'Cloudy',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'Australia',
                'country_code' => 'AU',
                'capital' => 'Canberra',
                'currency' => 'AUD',
                'risk_score' => 20,
                'risk_level' => 'Low',
                'temperature' => 24,
                'weather' => 'Sunny',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'Japan',
                'country_code' => 'JP',
                'capital' => 'Tokyo',
                'currency' => 'JPY',
                'risk_score' => 25,
                'risk_level' => 'Low',
                'temperature' => 23,
                'weather' => 'Clear',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'India',
                'country_code' => 'IN',
                'capital' => 'New Delhi',
                'currency' => 'INR',
                'risk_score' => 55,
                'risk_level' => 'Medium',
                'temperature' => 31,
                'weather' => 'Sunny',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'Brazil',
                'country_code' => 'BR',
                'capital' => 'Brasilia',
                'currency' => 'BRL',
                'risk_score' => 50,
                'risk_level' => 'Medium',
                'temperature' => 30,
                'weather' => 'Rain',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'Germany',
                'country_code' => 'DE',
                'capital' => 'Berlin',
                'currency' => 'EUR',
                'risk_score' => 18,
                'risk_level' => 'Low',
                'temperature' => 19,
                'weather' => 'Cloudy',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'Singapore',
                'country_code' => 'SG',
                'capital' => 'Singapore',
                'currency' => 'SGD',
                'risk_score' => 12,
                'risk_level' => 'Low',
                'temperature' => 32,
                'weather' => 'Sunny',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'country_name' => 'Ukraine',
                'country_code' => 'UA',
                'capital' => 'Kyiv',
                'currency' => 'UAH',
                'risk_score' => 90,
                'risk_level' => 'High',
                'temperature' => 22,
                'weather' => 'Cloudy',
                'created_at' => now(),
                'updated_at' => now(),
            ]

        ]);
    }
}