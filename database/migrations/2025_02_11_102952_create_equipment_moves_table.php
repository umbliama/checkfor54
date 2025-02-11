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
        Schema::create('equipment_moves', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('send_date');
            $table->date('departure_date');
            $table->foreignId('from')->constrained('equipment_locations');
            $table->foreignId('to')->constrained('equipment_locations');
            $table->text('reason');
            $table->integer('expense');
            $table->foreignId('category_id')->constrained('equipment_categories');
            $table->string('size_id')->constrained('equipment_sizes');
            $table->string('series');
            $table->foreignId('equipment_id')->constrained('equipment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_moves');
    }
};
