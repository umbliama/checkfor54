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
        Schema::create('equipment_sizes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
        });

        DB::table('equipment_sizes')->insert([
            ['name' => '43', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '54', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '76', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '95', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '106', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '120', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '127', 'created_at' => now(), 'updated_at' => now()],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
