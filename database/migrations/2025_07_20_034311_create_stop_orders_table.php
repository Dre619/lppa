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
        Schema::create('stop_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('REMARKS')->nullable();
            $table->text('Location')->nullable();
            $table->text('District')->nullable();
            $table->text('Photo')->nullable();
            $table->text('Time')->nullable();
            $table->string('Name');
            $table->string('Plot_No')->nullable();
            $table->string('Phone_No')->nullable();
            $table->longText('Description_of__Development')->nullable();
            $table->string('Stage_of__Construction');
            $table->text('Observation_Notes')->nullable();
            $table->string('Inspection__Officer')->nullable();
            $table->string('Supervisor')->nullable();
            $table->string('Date', 50)->nullable();
            $table->string('Zoning');
            $table->longText('Picture')->nullable();
            $table->string('Response_Date', 50)->nullable();
            $table->text('Responded')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stop_orders');
    }
};
