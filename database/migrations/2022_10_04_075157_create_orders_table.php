<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('sub_total')->default(0);

            $table->string('number')->unique();
            $table->string('paymant_method');

            $table->enum('status', ['cancelled', 'completed', 'pending', 'refunded'])->default('pending');

            $table->foreignId('user_id')->index()->nullable()->constrained('users')->cascadeOnDelete();
            // $table->foreignId('order_address')->constrained('order_addresses')->cascadeOnDelete();

            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
