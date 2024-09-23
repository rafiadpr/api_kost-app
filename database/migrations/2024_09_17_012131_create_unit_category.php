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
        Schema::create('unit_category', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150)->comment('Fill with name of unit category');
            $table->string('type', 150)->comment('Fill with name of unit category');
            // $table->integer('index')->comment('fill with index for ordering')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_category');
    }
};
