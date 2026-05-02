<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gtin_prefixes', function (Blueprint $table) {
            $table->string('prefix')->primary();
            $table->string('country');
            $table->string('country_code', 3)->nullable()->index();
            $table->string('flag')->nullable();//emoji code #
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gtin_prefixes');
    }
};
