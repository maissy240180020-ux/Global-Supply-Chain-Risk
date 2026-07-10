<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {

            $table->decimal('gdp',15,2)->nullable();

            $table->decimal('inflation',5,2)->nullable();

            $table->bigInteger('population')->nullable();

            $table->decimal('latitude',10,6)->nullable();

            $table->decimal('longitude',10,6)->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {

            $table->dropColumn([
                'gdp',
                'inflation',
                'population',
                'latitude',
                'longitude'
            ]);

        });
    }
};