<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('color')->nullable();

            $table->unsignedInteger('price');
            $table->unsignedInteger('retail');

            $table->integer('height')->nullable();
            $table->integer('width')->nullable();
            $table->integer('length')->nullable();
            $table->integer('weight')->nullable();

            $table->boolean('active')->default(true);
            $table->boolean('shippable')->default(false);

            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
