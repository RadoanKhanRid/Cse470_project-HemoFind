<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
    Schema::table('blood_requests', function (Blueprint $table) {
        // Default to 'pending'. When 'done', we hide it.
        $table->string('status')->default('pending')->after('blood_type');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            //
        });
    }
};
