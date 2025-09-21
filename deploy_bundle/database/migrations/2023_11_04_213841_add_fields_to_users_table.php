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
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'oauth_id')) {
                $table->string('oauth_id')->nullable();
            }
            if (! Schema::hasColumn('users', 'oauth_type')) {
                $table->string('oauth_type')->nullable();
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable();
            }
            if (! Schema::hasColumn('users', 'cpf')) {
                $table->string('cpf', 20)->nullable();
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable();
            }
            if (! Schema::hasColumn('users', 'logged_in')) {
                $table->tinyInteger('logged_in')->default(0);
            }
            if (! Schema::hasColumn('users', 'banned')) {
                $table->tinyInteger('banned')->default(0);
            }
            if (! Schema::hasColumn('users', 'inviter')) {
                $table->integer('inviter')->nullable();
            }
            if (! Schema::hasColumn('users', 'inviter_code')) {
                $table->string('inviter_code', 25)->nullable();
            }
            if (! Schema::hasColumn('users', 'affiliate_revenue_share')) {
                $table->bigInteger('affiliate_revenue_share')->default(2);
            }
            if (! Schema::hasColumn('users', 'affiliate_cpa')) {
                $table->decimal('affiliate_cpa', 20, 2)->default(10);
            }
            if (! Schema::hasColumn('users', 'affiliate_baseline')) {
                $table->decimal('affiliate_baseline', 20, 2)->default(40);
            }
            if (! Schema::hasColumn('users', 'is_demo_agent')) {
                $table->tinyInteger('is_demo_agent')->default(0);
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 50)->default('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'oauth_id', 'oauth_type', 'avatar', 'last_name', 'cpf', 'phone',
                'logged_in', 'banned', 'inviter', 'inviter_code', 'affiliate_revenue_share',
                'affiliate_cpa', 'affiliate_baseline', 'is_demo_agent', 'status',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
