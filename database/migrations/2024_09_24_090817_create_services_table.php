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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')->references('id')->on('equipment');
            $table->unsignedBigInteger('contragent_id');
            $table->foreign('contragent_id')->references('id')->on('contragents');
            $table->string('service_number');
            $table->date('service_date');
            $table->date('shipping_date');
            $table->date('period_start_date')->nullable();
            $table->date('return_date')->nullable();
            $table->date('period_end_date')->nullable();
            $table->string('store')->nullable();
            $table->string('operating')->nullable();
            $table->string('commentary')->nullable();
            $table->enum('return_reason', ['project', 'rejected'])->nullable();
            $table->boolean('active');
            $table->float('income')->nullable();
            $table->string('hyperlink')->nullable();
        });


        Schema::create('service_subequipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subequipment_id');
            $table->date('shipping_date');
            $table->date('period_start_date');
            $table->string('commentary');
            $table->date('return_date')->nullable();
            $table->date('period_end_date')->nullable();
            $table->string('store')->nullable();
            $table->string('operating')->nullable();
            $table->unsignedBigInteger('service_id');
            $table->integer('income')->nullable();
            $table->enum('return_reason', allowed: ['project', 'rejected'])->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_subequipment');
    }
};
