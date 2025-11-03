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
        Schema::table('registration_areas', function (Blueprint $table) {
            $table->foreign(['district_id'])->references(['id'])->on('districts')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_areas', function (Blueprint $table) {
            $table->dropForeign('registration_areas_district_id_foreign');
        });
    }
};
