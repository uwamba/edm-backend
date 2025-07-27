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
        Schema::table('approval_steps', function (Blueprint $table) {
            // Make approver_id nullable
            $table->unsignedBigInteger('approver_id')->nullable()->change();

            // Add job_title_id column
            $table->unsignedBigInteger('job_title_id')->nullable()->after('approver_id');

            // Optional: Add foreign key constraint (if you want strict integrity)
            $table->foreign('job_title_id')->references('id')->on('job_titles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('approval_steps', function (Blueprint $table) {
            // Reverse changes
            $table->dropForeign(['job_title_id']);
            $table->dropColumn('job_title_id');
            $table->unsignedBigInteger('approver_id')->nullable(false)->change(); // revert to not nullable if needed
        });
    }
};
