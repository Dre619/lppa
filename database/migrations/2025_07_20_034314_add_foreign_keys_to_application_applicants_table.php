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
        Schema::table('application_applicants', function (Blueprint $table) {
            $table->foreign(['applicant_title_id'])->references(['id'])->on('applicant_titles')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['applicant_type_id'])->references(['id'])->on('applicant_types')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['application_id'])->references(['id'])->on('applications')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_applicants', function (Blueprint $table) {
            $table->dropForeign('application_applicants_applicant_title_id_foreign');
            $table->dropForeign('application_applicants_applicant_type_id_foreign');
            $table->dropForeign('application_applicants_application_id_foreign');
        });
    }
};
