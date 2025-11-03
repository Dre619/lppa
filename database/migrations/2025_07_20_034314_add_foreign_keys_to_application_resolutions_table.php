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
        Schema::table('application_resolutions', function (Blueprint $table) {
            $table->foreign(['application_id'])->references(['id'])->on('applications')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['resolution_id'])->references(['id'])->on('resolutions')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_resolutions', function (Blueprint $table) {
            $table->dropForeign('application_resolutions_application_id_foreign');
            $table->dropForeign('application_resolutions_resolution_id_foreign');
        });
    }
};
