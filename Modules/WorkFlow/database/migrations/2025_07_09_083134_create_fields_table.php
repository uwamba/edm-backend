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
        Schema::create('fields', function (Blueprint $table) {
    $table->id();
    $table->foreignId('form_id')->constrained('forms')->onDelete('cascade');
    $table->string('label');
    $table->string('type'); // text, select, checkbox, etc.
    $table->json('options')->nullable(); // for select/checkbox
    $table->boolean('required')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
