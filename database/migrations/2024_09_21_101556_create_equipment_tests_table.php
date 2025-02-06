<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_tests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('on_test_date');
            $table->date('test_date')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('equipment_locations');
            $table->integer('expense')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onDelete('cascade');
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')->references('id')->on('equipment_sizes')->onDelete('cascade');
            $table->string('series');
            $table->string('mex_vverx')->nullable();
            $table->string('mex_vniz')->nullable();
            $table->string('usilie')->nullable();
            $table->string('delay')->nullable();
            $table->string('hyperlink')->nullable();
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
