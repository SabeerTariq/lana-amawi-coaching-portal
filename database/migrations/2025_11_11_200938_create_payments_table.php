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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_program_id')->constrained('user_programs')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->enum('payment_type', ['contract_monthly', 'contract_one_time', 'additional_session'])->default('contract_monthly');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->decimal('amount', 10, 2);
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->integer('month_number')->nullable()->comment('For monthly payments: 1, 2, or 3');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_program_id', 'status']);
            $table->index(['payment_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
