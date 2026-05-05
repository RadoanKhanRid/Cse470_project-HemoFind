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
        Schema::table('donations', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            //
            Schema::table('donations', function (Blueprint $table) {
    $table->integer('age');
    $table->decimal('weight', 5, 2);
    $table->boolean('has_medical_conditions')->default(false);
    $table->date('last_tattoo_or_surgery')->nullable();
});
        });
    }
};
