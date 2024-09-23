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
        Schema::create('unit_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unit_id')->comment('Fill with id of unit')->nullable();
            $table->tinyInteger('is_available')->comment('Fill with "1" is unit available, fill with "0" if unit is unavailable')->default(0);
            $table->text('description')->comment('Fill with description');
            // $table->string('day')->comment('Fill with name of day');
            // $table->time('start_time')->comment('Fill with time of start_time');
            // $table->time('end_time')->comment('Fill with time of end_time');
            // $table->double('price')->nullable()->comment('Fill with price of unit');
            $table->timestamps();
            $table->softDeletes();

            $table->index('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_detail');
    }
};
