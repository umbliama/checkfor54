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
            $table->unsignedBigInteger('contragent_id');
            $table->foreign('contragent_id')->references('id')->on('contragents')->onDelete('cascade');
            $table->string('service_number');
            $table->date('service_date');       
            $table->float('full_income')->nullable();
            $table->boolean('active');
            $table->string('hyperlink')->nullable();
        });

        Schema::create('service_subequipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('shipping_date');
            $table->date('period_start_date')->nullable();
            $table->string('commentary')->nullable();
            $table->date('return_date')->nullable();
            $table->date('period_end_date')->nullable();
            $table->string('store')->nullable();
            $table->string('operating')->nullable();
            $table->float('income')->nullable();
            $table->enum('return_reason', ['project', 'rejected'])->nullable();
            $table->integer('subequipment_id');
            $table->unsignedBigInteger('service_equipment_id');
            $table->foreign('service_equipment_id')->references('id')->on('service_equipment')->onDelete('cascade');
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');

        });
    
        Schema::create('service_equipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->date('shipping_date');
            $table->date('period_start_date')->nullable();
            $table->date('return_date')->nullable();
            $table->date('period_end_date')->nullable();
            $table->string('store')->nullable();
            $table->string('operating')->nullable();
            $table->string('commentary')->nullable();
            $table->enum('return_reason', ['project', 'rejected'])->nullable();
            $table->float('income')->nullable();
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
