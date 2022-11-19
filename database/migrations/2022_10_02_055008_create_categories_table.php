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
            $table->string('image')->nullable();
            $table->mediumText('description');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
