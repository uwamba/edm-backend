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
        Schema::create('workflows', function (Blueprint $table) {
    $table->id();
    $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
    $table->foreignId('submission_id')->constrained('submissions')->onDelete('cascade');
    $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
