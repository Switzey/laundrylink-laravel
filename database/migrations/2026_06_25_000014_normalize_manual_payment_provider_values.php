<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            DB::table('payments')
                ->where('provider', '!=', 'manual')
                ->update([
                    'provider' => 'manual',
                    'authorization_url' => null,
                    'access_code' => null,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Manual payment provider normalization is not reversible.
    }
};
