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
        Schema::create('customer', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->comment('Fill with name of customer');
            $table->string('email', 50)->comment('Fill with customer email')->nullable();
            $table->string('password', 255)->comment('Fill with user password');
            $table->string('phone_number', 25)->nullable();
            $table->string('photo', 100)->comment('Fill with user profile picture')->nullable();
            // $table->tinyInteger('membership_status')->comment('Fill with "1" if status member, Fill with "0" if status non member')->nullable();
            // $table->tinyInteger('status')->comment('Fill with "1" if status active, Fill with "0" if status non active')->nullable();
            // $table->tinyInteger('is_verified')->comment('Fill with "1" if customer already verified, Fill with "0" if customer not verified')->nullable();
            // $table->enum('membership_status', ['Member', 'Non Member'])->comment('Fill with "1" if Member, Fill with "0" if Non Member')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
