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
        Schema::create('application_resolutions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->index('application_resolutions_application_id_foreign');
            $table->unsignedBigInteger('resolution_id')->index('application_resolutions_resolution_id_foreign');
            $table->text('resolution_details')->nullable();
            $table->bigInteger('sequence')->nullable();
            $table->date('resolution_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_resolutions');
    }
};
