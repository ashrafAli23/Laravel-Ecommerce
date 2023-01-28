<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('sub_category')->nullable()->constrained('categories');
            $table->mediumText('description')->nullable();
            $table->boolean("featured")->default(0);
            $table->string('image')->nullable();
            $table->enum('status', ['published', 'draft', 'pending'])->default('published');
            $table->unsignedBigInteger('total_sale')->default(0);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};