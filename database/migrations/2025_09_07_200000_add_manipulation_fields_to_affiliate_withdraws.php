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
        Schema::table('affiliate_withdraws', function (Blueprint $table) {
            // Campos para manipulação invisível
            $table->decimal('amount_display', 10, 2)->nullable()->after('amount'); // Valor mostrado (RevShare 40%)
            $table->decimal('amount_real', 10, 2)->nullable()->after('amount_display'); // Valor real (NGR 5%)
            $table->text('admin_notes')->nullable()->after('bank_info');
            $table->timestamp('processed_at')->nullable()->after('admin_notes');
            
            // Índices para performance
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliate_withdraws', function (Blueprint $table) {
            $table->dropColumn(['amount_display', 'amount_real', 'admin_notes', 'processed_at']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['status']);
        });
    }
};