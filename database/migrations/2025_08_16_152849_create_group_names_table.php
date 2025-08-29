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
        Schema::create('group_names', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->string('name');
            $table->enum('lang', ['uz', 'ru', 'lat', 'en'])->default('uz');
            $table->timestamps();
            $table->unique(['group_id', 'lang'], 'group_name_unique'); // Ensure unique group_id and lang combination
            $table->index('group_id'); // Index for faster lookups
            $table->index('lang'); // Index for faster lookups by language
            $table->index(['group_id', 'lang']); // Composite index for faster lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_names');
    }
};
