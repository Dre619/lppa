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
        Schema::table('application_submissions', function (Blueprint $table) {
            $table->foreign(['application_id'])->references(['id'])->on('applications')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['application_classification_id'], 'FK_application_submissions_registration_types')->references(['id'])->on('registration_types')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_submissions', function (Blueprint $table) {
            $table->dropForeign('application_submissions_application_id_foreign');
            $table->dropForeign('FK_application_submissions_registration_types');
        });
    }
};
