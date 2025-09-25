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
        Schema::create('configs_playfiver', function (Blueprint $table) {
            $table->id();
            $table->integer("rtp")->nullable();
            $table->boolean("limit_enable")->default(false);
            $table->decimal("limit_amount")->default(0);
            $table->integer("limit_hours")->default(1);
            $table->boolean("bonus_enable")->default(true);
            $table->boolean("edit")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configs_playfiver');

    }
};
