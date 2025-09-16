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
        Schema::create('user_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'agreement_sent', 'agreement_uploaded', 'approved', 'payment_requested', 'payment_completed', 'active', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->string('agreement_path')->nullable();
            $table->string('signed_agreement_path')->nullable();
            $table->string('signed_agreement_name')->nullable();
            $table->timestamp('agreement_sent_at')->nullable();
            $table->timestamp('agreement_uploaded_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('payment_requested_at')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_programs');
    }
};
