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
        Schema::create('product_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('unique_key')->unique();
            $table->integer('product_file_id');
            $table->string('product_title', 255);
            $table->text('product_description');
            $table->string('style', 255);
            $table->string('sanmar_mainframe_color', 255);
            $table->string('size', 255);
            $table->string('color_name', 255);
            $table->double('piece_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_detail');
    }
};
