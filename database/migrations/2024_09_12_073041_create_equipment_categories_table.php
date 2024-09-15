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
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });

        DB::table('equipment_categories')->insert([
            ['name' => 'ВЗД', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ЯСС', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ФД', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'КО', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ПК', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ХОМУТ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ПЕРЕВОДНИК', 'created_at' => now(), 'updated_at' => now()],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_categories');
    }
};
