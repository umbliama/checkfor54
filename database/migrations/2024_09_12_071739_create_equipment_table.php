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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('manufactor');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onDelete('cascade');
            $table->unsignedBigInteger('size_id');
            $table->foreign('size_id')->references('id')->on('equipment_sizes')->onDelete('cascade');
            $table->unsignedBigInteger('location_id')->after('size_id');
            $table->foreign('location_id')->references('id')->on('equipment_locations')->onDelete('cascade');
            $table->string('series')->nullable();
            $table->string('length')->nullable();
            $table->string('operating')->nullable();
            $table->date('manufactor_date')->nullable();
            $table->enum('status', ['new', 'good', 'satisfactory', 'bad', 'off','unknown'])->nullable();
            $table->text('notes')->nullable();
            $table->integer('price')->nullable();
            $table->text('commentary')->nullable();
            $table->string('zahodnost')->nullable();
            $table->string('stator_rotor')->nullable();
            $table->string('dlina_ds')->nullable();
            $table->string('narabotka_ds')->nullable();
            $table->string('rezbi')->nullable();
            $table->string('length_rezba')->nullable();
            $table->string('diameter')->nullable();
            $table->string('hyperlink')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
