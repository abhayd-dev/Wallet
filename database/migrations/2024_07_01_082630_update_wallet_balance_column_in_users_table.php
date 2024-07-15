<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWalletBalanceColumnInUsersTable extends Migration
{
    public function up()
    {
        // Modify existing column
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet_balance', 10, 2)->nullable()->default(0)->change();
        });
    }

    public function down()
    {
        // Revert changes if needed
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet_balance', 8, 2)->nullable(false)->default(0)->change();
        });
    }
}
