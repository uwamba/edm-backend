<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('submission_fields', function (Blueprint $table) {
            $table->unsignedInteger('repeat_index')->nullable()->after('value')->comment('Index of repeated group, null if single');
        });
    }

    public function down()
    {
        Schema::table('submission_fields', function (Blueprint $table) {
            $table->dropColumn('repeat_index');
        });
    }
};

