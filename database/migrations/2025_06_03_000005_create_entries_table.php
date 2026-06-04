<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // voice, photo, manual
            $table->string('status')->default('processing'); // processing, draft, final
            $table->string('title')->nullable();
            $table->longText('raw_transcript')->nullable();
            $table->json('ai_extracted_data')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->date('entry_date')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index(['workspace_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }
};
