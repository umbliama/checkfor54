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
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('column_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->foreignId('contragent_id')->nullable()->constrained()->nullOnDelete(); 
            $table->text('equipment')->nullable()->constrained()->nullOnDelete(); 
            $table->json('media_url')->nullable();
            $table->json('file_url')->nullable();
            $table->text('commentary')->nullable();
            $table->integer('position');
            $table->foreignId('employee_id')->nullable()->constrained('users');
            $table->foreignId('creator_id')->constrained(('users'));
        });
        Schema::create('block_subequipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained('blocks')->onDelete('cascade'); 
            $table->foreignId('subequipment_id')->constrained('equipment')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
