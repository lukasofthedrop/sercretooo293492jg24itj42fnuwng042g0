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
        Schema::create('affiliate_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->decimal('revshare_percentage', 5, 2)->default(20.00);
            $table->decimal('cpa_value', 10, 2)->default(50.00);
            $table->decimal('ngr_minimum', 10, 2)->default(100.00);
            $table->enum('tier', ['bronze', 'silver', 'gold', 'custom'])->default('bronze');
            $table->json('visible_metrics')->nullable();
            $table->boolean('can_see_ngr')->default(false);
            $table->boolean('can_see_deposits')->default(true);
            $table->boolean('can_see_losses')->default(true);
            $table->boolean('can_see_reports')->default(false);
            $table->enum('calculation_period', ['daily', 'weekly', 'monthly'])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_settings');
    }
};
