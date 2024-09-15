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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('manufactor');
            $table->unsignedBigInteger('category_id')->after('manufactor'); // Add the category_id column
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onDelete('set null');
            $table->unsignedBigInteger('size_id')->after('manufactor'); // Add the size_id column
            $table->foreign('size_id')->references('id')->on('equipment_categories')->onDelete('set null');
            $table->string('series');
            $table->date('manufactor_date');
            $table->enum('status',['new','good','satisfactory', 'bad', 'off']);
            $table->text('notes')->nullable();
            $table->integer('price');
            $table->text('commentary')->nullable();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
