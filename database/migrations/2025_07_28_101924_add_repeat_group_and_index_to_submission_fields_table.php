<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepeatGroupAndIndexToSubmissionFieldsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('submission_fields', 'repeat_group')) {
            Schema::table('submission_fields', function (Blueprint $table) {
                $table->integer('repeat_group')
                      ->nullable()
                      ->after('value')
                      ->comment('Group number for repeated child fields');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('submission_fields', 'repeat_group')) {
            Schema::table('submission_fields', function (Blueprint $table) {
                $table->dropColumn('repeat_group');
            });
        }
    }
}
