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
        Schema::create('equipment_sizes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('equipment_categories')->onDelete('cascade');

        });

        $categoryVZD = DB::table('equipment_categories')->where('id', '1')->first();

        DB::table('equipment_sizes')->insert([
            ['name' => '43', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '54', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '76', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '95', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '106', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '120', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '127', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '165', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '172', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '195', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '210', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '240', 'category_id' => $categoryVZD->id, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $categoryYSS = DB::table('equipment_categories')->where('id', '2')->first();

        DB::table('equipment_sizes')->insert([
            ['name' => '105', 'category_id' => $categoryYSS->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '108', 'category_id' => $categoryYSS->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '121', 'category_id' => $categoryYSS->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '165', 'category_id' => $categoryYSS->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '171', 'category_id' => $categoryYSS->id, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '203', 'category_id' => $categoryYSS->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $otherCategories = ['3', '4', '5', '6', '7'];
        foreach ($otherCategories as $category) {
            DB::table('equipment_sizes')->insert([
                ['name' => '43', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '54', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '76', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '95', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '106', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '120', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '127', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '165', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '172', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '195', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '210', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
                ['name' => '240', 'category_id' => $category, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
