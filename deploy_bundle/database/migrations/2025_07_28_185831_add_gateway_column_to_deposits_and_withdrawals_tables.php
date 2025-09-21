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
        Schema::table('deposits', function (Blueprint $table) {
            if (!Schema::hasColumn('deposits', 'gateway')) {
                $table->string('gateway')->nullable()->after('status');
            }
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            if (!Schema::hasColumn('withdrawals', 'gateway')) {
                $table->string('gateway')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            if (Schema::hasColumn('deposits', 'gateway')) {
                $table->dropColumn('gateway');
            }
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            if (Schema::hasColumn('withdrawals', 'gateway')) {
                $table->dropColumn('gateway');
            }
        });
    }
};
