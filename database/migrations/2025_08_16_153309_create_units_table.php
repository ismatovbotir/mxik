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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('product_id');
            $table->integer('status')->default(0);
            $table->string('name')->nullable();
            //$table->string('nameRu')->nullable();
            //$table->string('nameLat')->nullable();
            $table->string('packageType')->default('1');

            //$table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            //$table->timestamps(); --- IGNORE ---
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
