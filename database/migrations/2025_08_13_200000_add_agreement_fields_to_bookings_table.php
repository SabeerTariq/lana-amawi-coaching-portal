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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('signed_agreement_path')->nullable()->after('status');
            $table->string('signed_agreement_name')->nullable()->after('signed_agreement_path');
            $table->timestamp('agreement_uploaded_at')->nullable()->after('signed_agreement_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'signed_agreement_path',
                'signed_agreement_name',
                'agreement_uploaded_at'
            ]);
        });
    }
};
