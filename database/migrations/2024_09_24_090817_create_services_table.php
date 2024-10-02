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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('equipment_id'); // Add the equipment_id column
            $table->foreign('equipment_id')->references('id')->on('equipment');
            $table->unsignedBigInteger('contragent_id'); // Add the equipment_id column
            $table->foreign('contragent_id')->references('id')->on('contragents');
            $table->date('shipping_date');
            $table->date('period_start_date');
            $table->date('return_date');
            $table->date('period_end_date');
            $table->string('store');
            $table->string('operating');
            $table->enum('return_reason',['project','rejected']);
            $table->boolean('active');
            $table->integer('income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
