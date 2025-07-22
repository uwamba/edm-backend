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
        Schema::table('fields', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_field_id')->nullable()->after('form_id');

            $table->foreign('parent_field_id')
                  ->references('id')
                  ->on('fields')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            $table->dropForeign(['parent_field_id']);
            $table->dropColumn('parent_field_id');
        });
    }
};
