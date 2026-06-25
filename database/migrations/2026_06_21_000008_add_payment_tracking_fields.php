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
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid')->after('total');
            }

            if (! Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->after('order_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('payments', 'authorization_url')) {
                $table->string('authorization_url')->nullable()->after('reference');
            }

            if (! Schema::hasColumn('payments', 'access_code')) {
                $table->string('access_code')->nullable()->after('authorization_url');
            }

            if (! Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('payments', 'metadata')) {
                $table->json('metadata')->nullable()->after('paid_at');
            }
        });

        if (! Schema::hasIndex('payments', 'payments_reference_unique')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->unique('reference', 'payments_reference_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep rollback conservative because the base create migrations now include
        // these columns for fresh installs.
    }
};
