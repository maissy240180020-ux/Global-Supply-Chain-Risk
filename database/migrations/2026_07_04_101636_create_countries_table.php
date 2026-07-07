<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('countries', function (Blueprint $table) {
        $table->id();
        $table->string('country_name');
        $table->string('country_code', 10);
        $table->string('capital');
        $table->string('currency');
        $table->decimal('risk_score', 5, 2)->default(0);
        $table->string('risk_level');
        $table->decimal('temperature', 5, 2)->nullable();
        $table->string('weather')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
