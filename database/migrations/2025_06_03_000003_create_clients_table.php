<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('company')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_housenumber', 10)->nullable();
            $table->string('address_postcode', 10)->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_country', 2)->default('NL');
            $table->string('kvk_number', 20)->nullable();
            $table->string('btw_number', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('workspace_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
