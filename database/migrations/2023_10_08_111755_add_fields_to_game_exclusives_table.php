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
        if (! Schema::hasTable('game_exclusives')) {
            return;
        }

        Schema::table('game_exclusives', function (Blueprint $table) {
            $table->text('loseResults')->nullable();
            $table->text('demoWinResults')->nullable();
            $table->text('winResults')->nullable();
            $table->text('iconsJson')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('game_exclusives')) {
            return;
        }

        Schema::table('game_exclusives', function (Blueprint $table) {
            $table->dropColumn(['loseResults', 'demoWinResults', 'winResults', 'iconsJson']);
        });
    }
};
