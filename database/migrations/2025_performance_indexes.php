<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Índices críticos para performance
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index(['game_id', 'created_at']);
            $table->index('type');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
        
        Schema::table('affiliate_histories', function (Blueprint $table) {
            $table->index(['inviter', 'commission_type', 'status']);
        });
    }
    
    public function down()
    {
        // Reverter se necessário
    }
}
