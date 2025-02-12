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
            $table->text('commentary')->nullable();
            $table->integer('contract')->nullable();
            $table->decimal('full_income',20,2)->nullable();
            $table->boolean('active');
            $table->string('hyperlink')->nullable();
        });
        
        Schema::create('service_equipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->date('shipping_date')->nullable();
            $table->date('period_start_date')->nullable();
            $table->date('return_date')->nullable();
            $table->date('period_end_date')->nullable();
            $table->decimal('store',20,2)->nullable();
            $table->decimal('operating',20,2)->nullable();
            $table->string('commentary')->nullable();
            $table->enum('return_reason', ['project', 'rejected'])->nullable();
            $table->decimal('income',20,2)->nullable();
        });
        
        Schema::create('service_subequipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('shipping_date')->nullable();
            $table->date('period_start_date')->nullable();
            $table->string('commentary')->nullable();
            $table->date('return_date')->nullable();
            $table->date('period_end_date')->nullable();
            $table->decimal('store',20,2)->nullable();
            $table->decimal('operating',20,2)->nullable();
            $table->decimal('income',20,2)->nullable();
            $table->enum('return_reason', ['project', 'rejected'])->nullable();
            $table->unsignedBigInteger('subequipment_id');
            $table->foreign('subequipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->unsignedBigInteger('service_equipment_id');
            $table->foreign('service_equipment_id')->references('id')->on('service_equipment')->onDelete('cascade');
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');        });
        
            Schema::create('services_extra', function (Blueprint $table) {
                $table->id();
                $table->date('shipping_date');
                $table->enum('type', ['transfer','repair_vzd','repair_yss', 'test_vzd', 'test_yss','replace_vzd','replace_yss', 'kern']);
                $table->string('commentary');
                $table->unsignedBigInteger('service_id');
                $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
                $table->integer('price')->nullable();
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
