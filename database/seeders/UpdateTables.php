<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UpdateTables extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        Schema::table('settings', function ($table) {
            if (! Schema::hasColumn('settings', 'digito_is_enable')) {
                $table->boolean('digito_is_enable')->default(false);
            }
            if (! Schema::hasColumn('settings', 'ezzepay_is_enable')) {
                $table->boolean('ezzepay_is_enable')->default(false);
            }
            if (! Schema::hasColumn('settings', 'saque')) {
                $table->string('saque')->default('ezzepay');
            }
        });

        Schema::table('gateways', function ($table) {
            foreach ([
                'digito_uri', 'digito_client', 'digito_secret',
                'ezze_uri', 'ezze_client', 'ezze_secret', 'ezze_user', 'ezze_senha',
            ] as $column) {
                if (! Schema::hasColumn('gateways', $column)) {
                    $table->string($column)->nullable();
                }
            }
        });

        Schema::table('withdrawals', function ($table) {
            if (! Schema::hasColumn('withdrawals', 'cpf')) {
                $table->string('cpf')->nullable();
            }
            if (! Schema::hasColumn('withdrawals', 'name')) {
                $table->string('name')->nullable();
            }
        });

        Schema::table('custom_layouts', function ($table) {
            foreach ([
                'image_Jackpot', 'image_hot1', 'image_hot2', 'image_hot3', 'image_hot4', 'image_hot5',
                'banner_deposito1', 'banner_deposito2', 'banner_registro', 'banner_login', 'image_navbar',
                'popup_fluatuante', 'link_fluatuante', 'popup2_fluatuante', 'link_fluatuante2',
                'idPixelFC', 'idPixelGoogle', 'link_suporte',
            ] as $column) {
                if (! Schema::hasColumn('custom_layouts', $column)) {
                    $table->string($column)->nullable();
                }
            }
        });
    }
}
