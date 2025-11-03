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
        Schema::table('applications', function (Blueprint $table) {
            $table->foreign(['change_land_use_form'])->references(['id'])->on('land_uses')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['change_land_use_to'])->references(['id'])->on('land_uses')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['change_of_use_stage_id'])->references(['id'])->on('change_of_use')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['current_use_of_land_id'])->references(['id'])->on('land_uses')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['development_area_id'])->references(['id'])->on('development_areas')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['district_id'])->references(['id'])->on('districts')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['registration_area_id'])->references(['id'])->on('registration_areas')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['registration_organization_id'])->references(['id'])->on('registration_organizations')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['application_classification_id'], 'FK_applications_registration_types')->references(['id'])->on('registration_types')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign('applications_change_land_use_form_foreign');
            $table->dropForeign('applications_change_land_use_to_foreign');
            $table->dropForeign('applications_change_of_use_stage_id_foreign');
            $table->dropForeign('applications_current_use_of_land_id_foreign');
            $table->dropForeign('applications_development_area_id_foreign');
            $table->dropForeign('applications_district_id_foreign');
            $table->dropForeign('applications_registration_area_id_foreign');
            $table->dropForeign('applications_registration_organization_id_foreign');
            $table->dropForeign('FK_applications_registration_types');
        });
    }
};
