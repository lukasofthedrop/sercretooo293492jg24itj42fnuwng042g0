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
        Schema::table('gateways', function (Blueprint $table) {
            $table->string('aureolink_client_id')->nullable();
            $table->string('aureolink_client_secret')->nullable();
            $table->string('aureolink_webhook_url')->nullable();
            $table->boolean('aureolink_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn([
                'aureolink_client_id',
                'aureolink_client_secret',
                'aureolink_webhook_url',
                'aureolink_enabled'
            ]);
        });
    }
}; 