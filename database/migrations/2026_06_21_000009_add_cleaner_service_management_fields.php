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
        Schema::table('cleaners', function (Blueprint $table) {
            if (! Schema::hasColumn('cleaners', 'opening_hours')) {
                $table->string('opening_hours')->nullable()->after('turnaround_time');
            }

            if (! Schema::hasColumn('cleaners', 'is_available')) {
                $table->boolean('is_available')->default(true)->after('opening_hours');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::table('cleaners', function (Blueprint $table) {
            if (Schema::hasColumn('cleaners', 'is_available')) {
                $table->dropColumn('is_available');
            }

            if (Schema::hasColumn('cleaners', 'opening_hours')) {
                $table->dropColumn('opening_hours');
            }
        });
    }
};
