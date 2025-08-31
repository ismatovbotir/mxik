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
        Schema::create('products', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('group_id')->constrained();
            $table->integer('status')->default(0);
            $table->string('product_id')->nullable();
            $table->text('name')->index();
            //$table->text('mxikNameRu')->nullable();
            //$table->text('mxikNameLat')->nullable();
            $table->integer('label')->default(0);
            $table->string('gtin', 14)->nullable()->index();
            $table->string('gtin_id')->nullable()->index();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('gtin_id')->references('id')->on('gtins');
            //$table->id(); --- IGNORE ---
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
