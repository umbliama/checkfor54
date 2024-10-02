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
        Schema::create('equipment_tests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('test_date');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('equipment_locations');
            $table->integer('expense');
            $table->text('description');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onDelete('cascade');
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')->references('id')->on('equipment_sizes')->onDelete('cascade');
            $table->string('series');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_tests');
    }
};
