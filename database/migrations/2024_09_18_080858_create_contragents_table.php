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
        Schema::create('contragents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->enum('agentTypeLegal', ['OOO','OAO','ZAO','PAO','individual']);
            $table->string('country');
            $table->string('name');
            $table->string('fullname')->nullable();
            $table->string('inn');
            $table->string('kpp')->nullable();
            $table->string('ogrn')->nullable();
            $table->text('reason')->nullable();
            $table->string('notes')->nullable();
            $table->text('commentary')->nullable();
            $table->string('group')->nullable();
            $table->string('bankname')->nullable();
            $table->string('bank_bik')->nullable();
            $table->string('bank_kpp')->nullable();
            $table->string('bank_inn')->nullable();
            $table->string('bank_rs')->nullable();
            $table->string('bank_ca')->nullable();
            $table->text('bank_commnetary')->nullable();
            $table->boolean('supplier');
            $table->boolean('customer');
            $table->string('address')->nullable();
            $table->string('site')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('contact_person_notes')->nullable();
            $table->string('contact_person_commentary')->nullable();
            $table->boolean('status')->nullable();
            $table->string('avatar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contragents');
    }
};
