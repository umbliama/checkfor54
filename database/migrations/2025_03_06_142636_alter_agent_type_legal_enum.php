<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contragents', function (Blueprint $table) {
            $table->string('agentTypeLegal_new')->nullable();
        });

        DB::statement("UPDATE contragents SET agentTypeLegal_new = agentTypeLegal");

        Schema::table('contragents', function (Blueprint $table) {
            $table->dropColumn('agentTypeLegal');
        });

        Schema::table('contragents', function (Blueprint $table) {
            $table->renameColumn('agentTypeLegal_new', 'agentTypeLegal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contragents', function (Blueprint $table) {
            $table->string('agentTypeLegal_old')->nullable();
        });

        DB::statement("UPDATE contragents SET agentTypeLegal_old = agentTypeLegal");

        Schema::table('contragents', function (Blueprint $table) {
            $table->dropColumn('agentTypeLegal');
        });

        Schema::table('contragents', function (Blueprint $table) {
            $table->renameColumn('agentTypeLegal_old', 'agentTypeLegal');
        });
    }
};
