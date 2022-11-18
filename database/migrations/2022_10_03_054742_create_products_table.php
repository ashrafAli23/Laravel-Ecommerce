<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('slug')->index();
            $table->string('name')->unique();

            $table->mediumText('description');

            $table->longText('images');

            $table->string('unit')->nullable();

            $table->integer('current_stock');

            $table->string('tax')->nullable();
            $table->string('tax_type')->nullable();

            $table->unsignedInteger('price');

            $table->boolean('active')->default(true);


            $table->foreignId('category_id')->index()->constrained('categories')->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->index()->constrained('brands')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
