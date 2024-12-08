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
        Schema::create('incident_subequipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('block_id')->constrained('blocks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_subequipment');
    }
};
