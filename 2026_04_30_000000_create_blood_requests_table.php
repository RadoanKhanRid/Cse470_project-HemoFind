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
        Schema::create('blood_requests', function (Blueprint $header) {
            $header->id();
            $header->string('patient_name');
            $header->string('blood_group');
            $header->string('hospital_name');
            $header->string('area');
            $header->decimal('latitude', 10, 8)->nullable();
            $header->decimal('longitude', 11, 8)->nullable();
            $header->string('urgency')->default('Urgent'); // Urgent, Scheduled
            $header->string('contact_number');
            $header->text('description')->nullable();
            $header->timestamps();
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
