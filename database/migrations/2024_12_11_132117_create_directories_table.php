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
        Schema::create('directories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('sale_id')->nullable();
            $table->foreignId('service_id')->nullable();
            $table->foreignId('equipment_id')->nullable();
            $table->foreignId('test_id')->nullable();
            $table->foreignId('repair_id')->nullable();
            $table->foreignId('price_id')->nullable();
            $table->json('files')->nullable();
            $table->text('commentary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directories');
    }
};
