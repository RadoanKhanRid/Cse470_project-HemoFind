<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->integer('age')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->boolean('has_medical_conditions')->default(false);
            $table->date('last_tattoo_or_surgery')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['age', 'weight', 'has_medical_conditions', 'last_tattoo_or_surgery']);
        });
    }
};