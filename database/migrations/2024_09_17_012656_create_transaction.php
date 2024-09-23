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
        Schema::create('transaction', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->comment('Fill with id of customer');
            $table->string('name', 100)->comment('Fill with name of customer');
            $table->string('phone_number', 25)->comment('Fill with phone_number of customer');
            $table->string('email', 50)->comment('Fill with customer email')->nullable();
            $table->dateTime('order_date');
            $table->double('total_price')->comment('Fill with total price from detail')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
