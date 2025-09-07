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
        Schema::table('affiliate_settings', function (Blueprint $table) {
            $table->decimal('revshare_display', 5, 2)->default(20.00)->after('revshare_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_settings', function (Blueprint $table) {
            $table->dropColumn('revshare_display');
        });
    }
};
