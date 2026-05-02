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
        Schema::create('class_codes', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('new_mxik_code')->nullable();
            $table->foreign('new_mxik_code')->references('id')->on('class_codes')->nullOnDelete();
            $table->string('status')->default('3');
            $table->unsignedInteger('class_group_id')->nullable();
            $table->foreign('class_group_id')->references('id')->on('class_groups')->nullOnDelete();
            $table->string('name');
            $table->string('gtin')->nullable()->index();
            $table->boolean('label')->default(false);
            $table->boolean('labelForCheck')->default(false);
            $table->boolean('usePackage')->default(false);
            $table->boolean('cashSale')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_codes');
    }
};
