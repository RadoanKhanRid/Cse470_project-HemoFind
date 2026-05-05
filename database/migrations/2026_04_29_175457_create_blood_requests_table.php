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
    Schema::create('blood_requests', function (Blueprint $table) {
        $table->id();
        $table->string('patient_name');
        $table->string('blood_type');
        $table->string('hospital_name');
        $table->date('required_date'); // The deadline
        $table->text('note')->nullable();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
