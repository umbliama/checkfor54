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
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('status_new')->nullable();
        });

        DB::statement("UPDATE equipment SET status_new = status");

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('status_old')->nullable();
        });

        DB::statement("UPDATE equipment SET status_old = status");

        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('equipment', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};
