<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // transcription, extraction, ocr
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('provider')->nullable(); // whisper, claude, etc.
            $table->string('input_path')->nullable();
            $table->json('output')->nullable();
            $table->unsignedInteger('tokens_used')->default(0);
            $table->decimal('cost', 8, 4)->default(0);
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['entry_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_jobs');
    }
};
