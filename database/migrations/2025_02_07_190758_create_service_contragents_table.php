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
        Schema::create('service_contragents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('contragent_id')->constrained('contragents');
            $table->string('commentary')->nullable();
            $table->date('shipping_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_contragents');
    }
};
