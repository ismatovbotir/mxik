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
        Schema::create('gtins', function (Blueprint $table) {
            $table->string('id', 3)->primary();
            $table->string('nameUz')->nullable();
            $table->string('nameRu')->nullable();
            $table->string('nameEn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gtins');
    }
};
