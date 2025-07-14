<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            // 🔥 Add validations if missing
            if (!Schema::hasColumn('fields', 'validations')) {
                $table->json('validations')->nullable()->after('required');
            }

            // 🔥 Add conditions if missing
            if (!Schema::hasColumn('fields', 'conditions')) {
                $table->json('conditions')->nullable()->after('validations');
            }

            // 🔥 Add parentField if missing
            if (!Schema::hasColumn('fields', 'parentField')) {
                $table->string('parentField')->nullable()->after('conditions');
            }

            // 🔥 Add parentMapping if missing
            if (!Schema::hasColumn('fields', 'parentMapping')) {
                $table->json('parentMapping')->nullable()->after('parentField');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            if (Schema::hasColumn('fields', 'validations')) {
                $table->dropColumn('validations');
            }
            if (Schema::hasColumn('fields', 'conditions')) {
                $table->dropColumn('conditions');
            }
            if (Schema::hasColumn('fields', 'parentField')) {
                $table->dropColumn('parentField');
            }
            if (Schema::hasColumn('fields', 'parentMapping')) {
                $table->dropColumn('parentMapping');
            }
        });
    }
};
