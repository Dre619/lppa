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
        Schema::table('enforcements', function (Blueprint $table) {
            $table->foreign(['stop_order_id'])->references(['id'])->on('stop_orders')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enforcements', function (Blueprint $table) {
            $table->dropForeign('enforcements_stop_order_id_foreign');
        });
    }
};
