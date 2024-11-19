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
        Schema::create('equipment_prices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('category_id'); // Add the category_id column
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onDelete('set null');
            $table->unsignedBigInteger('size_id'); // Add the size_id column
            $table->foreign('size_id')->references('id')->on('equipment_sizes')->onDelete('set null');
            $table->unsignedBigInteger('contragent_id'); // Add the equipment_id column
            $table->foreign('contragent_id')->references('id')->on('contragents');
            $table->date('store_date');
            $table->string('notes');
            $table->float('store_price');
            $table->float('operation_price');
            $table->boolean('archive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_price');
    }
};
