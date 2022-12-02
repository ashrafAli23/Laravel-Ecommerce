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
            $table->enum('stock_status', ['in_stock', 'out_stock'])->default('in_stock');
            $table->unsignedBigInteger('stock_qty')->default(10);
            $table->unsignedInteger('shipping_cost');
            $table->boolean('free_shipping')->default(false);
            $table->string('color')->nullable();
            $table->integer('height')->nullable();
            $table->integer('width')->nullable();
            $table->integer('length')->nullable();
            $table->integer('weight')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
