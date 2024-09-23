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
        Schema::create('unit', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('unit_category_id', 100)->comment('Fill with id from table category');
            $table->string('name', 100)->comment('Fill with name of unit');
            $table->double('price')->nullable()->comment('Fill with price of unit');
            // $table->string('photo', 100)->comment('Fill with photo of unit')->nullable();
            // $table->double('nominal_percentage')->nullable()->comment('Fill when status = no');
            // $table->enum('down_payment', ['yes', 'no'])->comment('Fill with type of down payment');
            $table->timestamps();
            $table->softDeletes();

            $table->index('unit_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};
