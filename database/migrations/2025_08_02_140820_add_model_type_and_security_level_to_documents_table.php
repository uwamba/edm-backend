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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'model_type_id')) {
                $table->foreignId('model_type_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete(); // e.g., "Policy", "Contract"
            }

            if (!Schema::hasColumn('documents', 'security_level')) {
                $table->string('security_level')
                    ->nullable(); // e.g., "Confidential"
            }
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'model_type_id')) {
                // Drop foreign key first before dropping column
                $table->dropForeign(['model_type_id']);
                $table->dropColumn('model_type_id');
            }

            if (Schema::hasColumn('documents', 'security_level')) {
                $table->dropColumn('security_level');
            }
        });
    }
};
