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
            $table->unsignedBigInteger('contragent_id');
            $table->foreign('contragent_id')->references('id')->on('contragents');
            $table->string('sale_number');
            $table->date('sale_date');
            $table->enum('status', ['credit', 'full', 'pred'])->nullable();
            $table->integer('price')->nullable();
            $table->text('commentary')->nullable();
            $table->date('shipping_date')->nullable();
            $table->text('hyperlink')->nullable();
        });

        Schema::create('sale_equipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('sale');
            $table->foreign('sale')->references('id')->on('sale')->onDelete('cascade');
            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')->references('id')->on('equipment')->onDelete('cascade');
            $table->date('shipping_date')->nullable();
            $table->string('commentary')->nullable();
            $table->float('price')->nullable();
        });

        Schema::create('sale_subequipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')->references('id')->on('equipment');
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sale')->onDelete('cascade');
            $table->date('shipping_date');
            $table->string('commentary')->nullable();
            $table->unsignedBigInteger('sale_equipment_id');
            $table->foreign('sale_equipment_id')->references('id')->on('sale_equipment')->onDelete('cascade');

            $table->integer('price')->nullable();
        });
        Schema::create('sale_extra', function (Blueprint $table) {
            $table->id();
            $table->date('shipping_date');
            $table->enum('type', ['transfer','repair_vzd','repair_yss', 'test_vzd', 'test_yss','replace_vzd','replace_yss', 'kern']);
            $table->string('commentary');
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sale')->onDelete('cascade');
            $table->integer('price')->nullable();
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
