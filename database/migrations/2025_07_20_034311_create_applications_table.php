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
        Schema::create('applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('change_of_use_stage_id')->nullable()->index('applications_change_of_use_stage_id_foreign');
            $table->unsignedBigInteger('registration_organization_id')->nullable()->index('applications_registration_organization_id_foreign');
            $table->unsignedBigInteger('application_classification_id')->nullable()->index('fk_applications_registration_types');
            $table->unsignedBigInteger('registration_area_id')->nullable()->index('applications_registration_area_id_foreign');
            $table->bigInteger('registration_number')->nullable();
            $table->string('registration_sub_number')->nullable();
            $table->string('application_id')->unique();
            $table->date('application_date')->nullable();
            $table->boolean('is_institution')->default(false);
            $table->string('institution_name')->nullable();
            $table->string('parcel_id')->nullable();
            $table->string('clean_parcel_id')->nullable();
            $table->string('affected_area')->nullable();
            $table->string('sub_plot_number')->nullable();
            $table->unsignedBigInteger('district_id')->nullable()->index('applications_district_id_foreign');
            $table->unsignedBigInteger('development_area_id')->nullable()->index('applications_development_area_id_foreign');
            $table->string('development_sub_area')->nullable();
            $table->string('local_area')->nullable();
            $table->integer('print_order')->nullable()->default(0);
            $table->integer('global_key')->nullable();
            $table->string('application_status')->nullable()->default('pending');
            $table->timestamps();
            $table->unsignedBigInteger('current_use_of_land_id')->nullable()->index('applications_current_use_of_land_id_foreign');
            $table->unsignedBigInteger('change_land_use_form')->nullable()->index('applications_change_land_use_form_foreign');
            $table->unsignedBigInteger('change_land_use_to')->nullable()->index('applications_change_land_use_to_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
