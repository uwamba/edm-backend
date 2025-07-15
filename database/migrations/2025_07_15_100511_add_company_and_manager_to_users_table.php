<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('email');
            $table->string('job_title')->nullable()->after('company_id');
            $table->unsignedBigInteger('manager_id')->nullable()->after('job_title');

            // Foreign key constraints (optional)
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['company_id', 'job_title', 'manager_id']);
        });
    }
};
