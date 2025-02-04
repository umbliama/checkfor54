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
        Schema::create('contr_sales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('shipping_date')->nullable();
            $table->text('commentary')->nullable();
            $table->unsignedBigInteger('contragent_id');
            $table->unsignedBigInteger('sale_id');
            $table->foreign('contragent_id')->references('id')->on('contragents')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contr_sales');
    }
};
