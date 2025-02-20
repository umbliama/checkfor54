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
            $table->foreignId('move_id')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('creator_id')->nullable();
            $table->text('commentary')->nullable();
        });

        Schema::create('directory_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('directory_id')->constrained()->onDelete('cascade'); 
            $table->string('file_path');
            $table->string('file_name')->nullable(); 
            $table->string('mime_type')->nullable(); 
            $table->integer('user_id');
            $table->integer('size')->nullable(); 
            $table->timestamps();
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
