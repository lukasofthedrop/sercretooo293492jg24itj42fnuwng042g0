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
            $table->boolean('digito_is_enable')->default(false);
            $table->boolean('ezzepay_is_enable')->default(false);
            $table->string('saque')->default("ezzepay");

        });
        Schema::table('gateways', function ($table) {
            $table->string('digito_uri')->nullable();
            $table->string('digito_client')->nullable();
            $table->string('digito_secret')->nullable();
            $table->string('ezze_uri')->nullable();
            $table->string('ezze_client')->nullable();
            $table->string('ezze_secret')->nullable();
            $table->string('ezze_user')->nullable();
            $table->string('ezze_senha')->nullable();

        });

        Schema::table('withdrawals', function ($table) {
            $table->string('cpf')->nullable();
            $table->string('name')->nullable();
          

        });
        Schema::table('custom_layouts', function ($table) {
            $table->string('image_Jackpot')->nullable();
            $table->string('image_hot1')->nullable();
            $table->string('image_hot2')->nullable();
            $table->string('image_hot3')->nullable();
            $table->string('image_hot4')->nullable();
            $table->string('image_hot5')->nullable();
            $table->string('banner_deposito1')->nullable();
            $table->string('banner_deposito2')->nullable();
            $table->string('banner_registro')->nullable();
            $table->string('banner_login')->nullable();
            $table->string('image_navbar')->nullable();
            $table->string('popup_fluatuante')->nullable();
            $table->string('link_fluatuante')->nullable();
            $table->string('popup2_fluatuante')->nullable();
            $table->string('link_fluatuante2')->nullable();
            $table->string('idPixelFC')->nullable();
            $table->string('idPixelGoogle')->nullable();
            $table->string('link_suporte')->nullable();

        });
    }
}
