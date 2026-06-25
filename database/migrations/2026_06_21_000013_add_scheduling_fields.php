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
        Schema::table('addresses', function (Blueprint $table) {
            if (! Schema::hasColumn('addresses', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('is_default');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'pickup_time_window')) {
                $table->string('pickup_time_window')->nullable()->after('pickup_date');
            }

            if (! Schema::hasColumn('orders', 'delivery_time_window')) {
                $table->string('delivery_time_window')->nullable()->after('delivery_date');
            }

            if (! Schema::hasColumn('orders', 'pickup_notes')) {
                $table->text('pickup_notes')->nullable()->after('paid_at');
            }

            if (! Schema::hasColumn('orders', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable()->after('pickup_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            foreach (['delivery_notes', 'pickup_notes', 'delivery_time_window', 'pickup_time_window'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'delivery_notes')) {
                $table->dropColumn('delivery_notes');
            }
        });
    }
};
