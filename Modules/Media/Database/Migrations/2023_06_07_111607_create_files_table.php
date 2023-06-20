<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedFloat('size');
            $table->string('url', 255);
            $table->string('mime_type');
            $table->foreignId('user_id')->index()->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('folder_id')->index()->constrained('folders')->cascadeOnDelete();
            $table->text('options')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
