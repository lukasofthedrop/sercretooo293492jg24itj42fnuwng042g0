<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UpdateTable2 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::table('affiliate_withdraws', function ($table) {
            $table->string('cpf')->nullable();
            $table->string('name')->nullable();
        });
    }
}
