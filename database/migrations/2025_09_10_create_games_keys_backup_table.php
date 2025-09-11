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
        Schema::create('games_keys_backup', function (Blueprint $table) {
            $table->id();
            $table->string('agent_name')->unique();
            $table->string('playfiver_url')->default('https://api.playfiver.com');
            $table->string('playfiver_secret');
            $table->string('playfiver_code');
            $table->string('playfiver_token');
            $table->decimal('saldo_agente', 20, 2)->default(0);
            $table->integer('priority')->default(1); // 1 = principal, 2 = backup, etc
            $table->boolean('is_active')->default(true);
            $table->string('webhook_url')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('last_used')->nullable();
            $table->integer('fail_count')->default(0);
            $table->timestamps();
        });

        // Inserir os dois agentes conhecidos
        DB::table('games_keys_backup')->insert([
            [
                'agent_name' => 'sorte365bet',
                'playfiver_url' => 'https://api.playfiver.com',
                'playfiver_secret' => 'f41adb6a-e15b-46b4-ad5a-1fc49f4745df',
                'playfiver_code' => 'sorte365bet',
                'playfiver_token' => 'a9aa0e61-9179-466a-8d7b-e22e7b473b8a',
                'saldo_agente' => 53152.40,
                'priority' => 1,
                'is_active' => true,
                'webhook_url' => 'https://bet.sorte365.fun/api/playfiver/webhook',
                'description' => 'Agente principal com saldo (emprestado)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'agent_name' => 'lucrativabt',
                'playfiver_url' => 'https://api.playfiver.com',
                'playfiver_secret' => '08cfba85-7652-4a00-903f-7ea649620eb2',
                'playfiver_code' => 'lucrativabt',
                'playfiver_token' => '80609b36-a25c-4175-92c5-c9a6f1e1b06e',
                'saldo_agente' => 0.00,
                'priority' => 2,
                'is_active' => true,
                'webhook_url' => 'https://lucrativa.bet/playfiver/webhook',
                'description' => 'Agente backup oficial do LucrativaBet',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games_keys_backup');
    }
};