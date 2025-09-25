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
        Schema::create('dashboard_groups', function (Blueprint $table) {
            $table->id();
            $table->string('gtin_id')->nullable()->index();
            $table->string('country')->nullable();
            $table->foreignId('group_id')->constrained();
            $table->string('name')->nullable();
            $table->integer('label')->default(0);
            $table->integer('total')->default(0);
            $table->date('dash_date')->nullable();

            $table->timestamps();
            $table->foreign('gtin_id')->references('id')->on('gtins');
            $table->unique(['gtin_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_groups');
    }
};
