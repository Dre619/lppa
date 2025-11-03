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
        Schema::table('sub_areas', function (Blueprint $table) {
            $table->foreign(['registration_area_id'])->references(['id'])->on('registration_areas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_areas', function (Blueprint $table) {
            $table->dropForeign('sub_areas_registration_area_id_foreign');
        });
    }
};
