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
        if (! Schema::hasIndex('reviews', 'reviews_order_id_unique')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique('order_id', 'reviews_order_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasIndex('reviews', 'reviews_order_id_unique')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropUnique('reviews_order_id_unique');
            });
        }
    }
};
