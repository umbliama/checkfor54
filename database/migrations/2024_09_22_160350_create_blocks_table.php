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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('column_id')->constrained()->onDelete('cascade'); 
            $table->string('type');
            $table->foreignId('contragent_id')->constrained();
            $table->foreignId('equipment_id')->constrained();
            $table->text('commentary')->nullable(); 
            $table->integer('position');
        });
        Schema::create('block_subequipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained()->onDelete('cascade'); // Block reference
            $table->foreignId('subequipment_id')->constrained('block_subequipment')->onDelete('cascade'); // Sub-equipment reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
