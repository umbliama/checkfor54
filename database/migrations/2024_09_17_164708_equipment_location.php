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
        Schema::create('equipment_locations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });     


        DB::table('equipment_locations')->insert([
            ['name' => 'г. Пермь', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'г. Нефтеюганск', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_location');        
    }
};
