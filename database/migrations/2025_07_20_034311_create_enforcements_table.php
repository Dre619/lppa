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
        Schema::create('enforcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('stop_order_id')->index('enforcements_stop_order_id_foreign');
            $table->date('date_issued')->nullable();
            $table->longText('description')->nullable();
            $table->text('current_stage')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enforcements');
    }
};
