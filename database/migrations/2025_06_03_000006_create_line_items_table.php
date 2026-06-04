<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->text('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 20)->default('uur'); // uur, stuk, m2, m, forfait
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('btw_rate', 5, 2)->default(21.00); // 21%, 9%, 0%
            $table->decimal('total', 10, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};
