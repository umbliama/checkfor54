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
        Schema::create('sale', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')->references('id')->on('equipment');
            $table->unsignedBigInteger('contragent_id');
            $table->foreign('contragent_id')->references('id')->on('contragents');
            $table->string('sale_number');
            $table->date('sale_date');
            $table->date('shipping_date');
            $table->string('commentary')->nullable();
            $table->enum('status', ['credit', 'full', 'pred'])->nullable();
            $table->integer('price')->nullable();

        });

        Schema::create('sale_extra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subequipment_id');
            $table->date('shipping_date');
            $table->string('commentary');
            $table->unsignedBigInteger('sale_id');
            $table->integer('price')->nullable();
            $table->enum('return_reason', ['project', 'rejected'])->nullable();
            $table->foreign('sale_id')->references('id')->on('sale')->onDelete('cascade');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale');
    }
};
