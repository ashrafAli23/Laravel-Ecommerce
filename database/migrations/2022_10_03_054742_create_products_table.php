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
            $table->string('name');
            $table->string('slug')->unique();
            $table->mediumText('description');
            $table->longText('images');
            $table->string('main_image');
            $table->string('featured')->nullable();
            $table->unsignedInteger('min_order_qty')->default(1);
            $table->unsignedInteger('max_order_qty')->default(1);
            $table->enum('product_type', ['digital', 'physical']);
            $table->string('barcode')->nullable();
            $table->string('tax')->nullable();
            $table->string('tax_type')->nullable();
            $table->string('video_link')->nullable();
            $table->enum('discount_type', ['amount', 'percentage'])->nullable();
            $table->unsignedFloat('discount')->default(0);
            $table->unsignedInteger('price');
            $table->unsignedDecimal('sale_price');
            $table->enum('status', ['published', 'draft', 'pending'])->default('published');
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