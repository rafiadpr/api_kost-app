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
        Schema::create('unit_asset', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unit_id')->comment('Fill with id of unit')->nullable();
            $table->string('name', 100)->comment('Fill with name of asset');
            $table->text('description')->comment('Fill with description');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_asset');
    }
};
