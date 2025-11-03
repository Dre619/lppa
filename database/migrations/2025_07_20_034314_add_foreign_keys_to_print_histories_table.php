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
        Schema::table('print_histories', function (Blueprint $table) {
            $table->foreign(['application_id'])->references(['id'])->on('applications')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['printed_by'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_histories', function (Blueprint $table) {
            $table->dropForeign('print_histories_application_id_foreign');
            $table->dropForeign('print_histories_printed_by_foreign');
        });
    }
};
