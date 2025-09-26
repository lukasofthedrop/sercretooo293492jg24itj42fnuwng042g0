<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Índices compostos para queries frequentes
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_user_date');
            $table->index(['game_id', 'type', 'created_at'], 'idx_game_type_date');
            $table->index(['status', 'created_at'], 'idx_status_date');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'created_at'], 'idx_user_status_date');
            $table->index(['type', 'created_at'], 'idx_type_date');
        });
        
        Schema::table('affiliate_histories', function (Blueprint $table) {
            $table->index(['inviter', 'commission_type', 'status'], 'idx_affiliate_commission');
            $table->index(['created_at', 'status'], 'idx_date_status');
        });
        
        Schema::table('wallets', function (Blueprint $table) {
            $table->index(['user_id', 'updated_at'], 'idx_wallet_user_update');
        });
        
        Schema::table('games', function (Blueprint $table) {
            $table->index(['provider_id', 'active'], 'idx_provider_active');
            $table->index(['category_id', 'views'], 'idx_category_views');
        });
        
        // Configurar tabelas para InnoDB com row format dinâmico
        DB::statement('ALTER TABLE orders ROW_FORMAT=DYNAMIC');
        DB::statement('ALTER TABLE transactions ROW_FORMAT=DYNAMIC');
        DB::statement('ALTER TABLE affiliate_histories ROW_FORMAT=DYNAMIC');
        
        // Analisar e otimizar tabelas
        DB::statement('ANALYZE TABLE orders, transactions, affiliate_histories, wallets, games');
    }
    
    public function down()
    {
        // Remover índices
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_user_date');
            $table->dropIndex('idx_game_type_date');
            $table->dropIndex('idx_status_date');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_user_status_date');
            $table->dropIndex('idx_type_date');
        });
        
        Schema::table('affiliate_histories', function (Blueprint $table) {
            $table->dropIndex('idx_affiliate_commission');
            $table->dropIndex('idx_date_status');
        });
        
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropIndex('idx_wallet_user_update');
        });
        
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex('idx_provider_active');
            $table->dropIndex('idx_category_views');
        });
    }
};
