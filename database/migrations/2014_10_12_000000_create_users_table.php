<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username', 60)->unique()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('avatar_id')->unsigned()->nullable();
            $table->text('permissions')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_super')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};