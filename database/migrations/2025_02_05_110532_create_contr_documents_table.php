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
        Schema::create('contr_documents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('contragent_id');
            $table->foreign('contragent_id')->references('id')->on('contragents')->onDelete('cascade');
            $table->json('commercials')->nullable();
            $table->json('contracts')->nullable();
            $table->json('transport')->nullable();
            $table->json('financial')->nullable();
            $table->json('adddocs')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contr_documents');
    }
};
