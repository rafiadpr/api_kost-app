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
        Schema::create('user_auth', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_roles_id', 100)->comment('Fill with id from table user_roles');
            $table->string('name', 100)->comment('Fill with name of user');
            $table->string('email', 50)->comment('Fill with user email for login');
            $table->string('password', 255)->comment('Fill with user password');
            $table->string('photo', 100)->comment('Fill with user profile picture')->nullable();
            $table->string('phone_number', 25)->comment('Fill with phone number of user');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_roles_id');
            $table->index('email');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_auth');
    }
};
