<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('entry_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('status')->default('draft'); // draft, sent, paid, overdue, cancelled
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('btw_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['workspace_id', 'status']);
            $table->index('invoice_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
