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

            if (!Schema::hasColumn('donations', 'cooldown_until')) {
                $table->timestamp('cooldown_until')->nullable();
            }

            if (!Schema::hasColumn('donations', 'hospital_name')) {
                $table->string('hospital_name')->nullable();
            }

            if (!Schema::hasColumn('donations', 'trust_score')) {
                $table->integer('trust_score')->default(0);
            }

            if (!Schema::hasColumn('donations', 'trust_tier')) {
                $table->string('trust_tier')->default('New Donor');
            }

            if (!Schema::hasColumn('donations', 'hospitals_visited_count')) {
                $table->integer('hospitals_visited_count')->default(0);
            }

            if (!Schema::hasColumn('donations', 'hospitals_visited_badge')) {
                $table->string('hospitals_visited_badge')->nullable();
            }
        });

        if (!Schema::hasTable('blood_inventory')) {

            Schema::create('blood_inventory', function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger('donation_id')->nullable();

                $table->string('donor_name');

                $table->string('blood_type', 10);

                $table->string('hospital_name')->nullable();

                $table->string('bag_number')->unique();

                $table->timestamp('collection_date')->nullable();

                $table->timestamp('expiry_date')->nullable();

                $table->string('status')->default('Available');

                $table->timestamps();

                $table->foreign('donation_id')
                    ->references('id')
                    ->on('donations')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('blood_inventory')) {
            Schema::dropIfExists('blood_inventory');
        }

        Schema::table('donations', function (Blueprint $table) {

            $columns = [
                'cooldown_until',
                'hospital_name',
                'trust_score',
                'trust_tier',
                'hospitals_visited_count',
                'hospitals_visited_badge',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('donations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};