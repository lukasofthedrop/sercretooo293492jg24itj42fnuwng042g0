<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'user_id')) {
                $table->index(['user_id', 'created_at'], 'legacy_idx_orders_user_date');
            }
            if (Schema::hasColumn('orders', 'game_id')) {
                $table->index(['game_id', 'created_at'], 'legacy_idx_orders_game_date');
            }
            if (Schema::hasColumn('orders', 'type')) {
                $table->index('type', 'legacy_idx_orders_type');
            }
        });

        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'user_id')) {
                $table->index(['user_id', 'created_at'], 'legacy_idx_transactions_user_date');
            }
            if (Schema::hasColumn('transactions', 'status')) {
                $table->index('status', 'legacy_idx_transactions_status');
            }
        });

        if (Schema::hasTable('affiliate_histories') && Schema::hasColumn('affiliate_histories', 'inviter')) {
            Schema::table('affiliate_histories', function (Blueprint $table) {
                $table->index(['inviter', 'commission_type', 'status'], 'legacy_idx_affiliate_combination');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                foreach ([
                    'legacy_idx_orders_user_date',
                    'legacy_idx_orders_game_date',
                    'legacy_idx_orders_type',
                ] as $index) {
                    try {
                        $table->dropIndex($index);
                    } catch (\Throwable $th) {
                        // ignore
                    }
                }
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                foreach ([
                    'legacy_idx_transactions_user_date',
                    'legacy_idx_transactions_status',
                ] as $index) {
                    try {
                        $table->dropIndex($index);
                    } catch (\Throwable $th) {
                        // ignore
                    }
                }
            });
        }

        if (Schema::hasTable('affiliate_histories')) {
            Schema::table('affiliate_histories', function (Blueprint $table) {
                try {
                    $table->dropIndex('legacy_idx_affiliate_combination');
                } catch (\Throwable $th) {
                    // ignore
                }
            });
        }
    }
};
