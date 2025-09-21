<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixGatewayColumn extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn("deposits", "gateway")) {
            Schema::table("deposits", function (Blueprint $table) {
                $table->string("gateway")->nullable()->default("default");
            });
        }

        if (!Schema::hasColumn("withdrawals", "gateway")) {
            Schema::table("withdrawals", function (Blueprint $table) {
                $table->string("gateway")->nullable()->default("default");
            });
        }
    }

    public function down()
    {
        Schema::table("deposits", function (Blueprint $table) {
            if (Schema::hasColumn("deposits", "gateway")) {
                $table->dropColumn("gateway");
            }
        });

        Schema::table("withdrawals", function (Blueprint $table) {
            if (Schema::hasColumn("withdrawals", "gateway")) {
                $table->dropColumn("gateway");
            }
        });
    }
}
