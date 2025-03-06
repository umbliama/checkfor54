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
        Schema::table('contragents', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE your_table_name
            MODIFY COLUMN agentTypeLegal ENUM(
                'OOO', 'OAO', 'ZAO', 'PAO', 'individual',
                'MMC', 'SC', 'ASC', 'CSC', 'DM', 'Cooperative',
                'AO', 'LLC', 'JSC', 'SOE', 'JV', 'WFOE',
                'TOO', 'NAO'
            )
        ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contragents', function (Blueprint $table) {
            DB::statement("
            ALTER TABLE your_table_name
            MODIFY COLUMN agentTypeLegal ENUM('OOO','OAO','ZAO','PAO','individual')
        ");
        });
    }
};
