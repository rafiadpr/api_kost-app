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
        Schema::create('transaction_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaction_id')->comment('Fill with id of transaction');
            $table->uuid('unit_id')->comment('Fill with id of unit');
            $table->double('price')->comment('Fill with price of unit or unit detail')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('transaction_id');
            $table->index('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_detail');
    }
};
