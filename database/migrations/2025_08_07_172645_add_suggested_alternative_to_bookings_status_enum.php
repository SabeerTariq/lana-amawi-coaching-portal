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
        // Since we're using string columns instead of enums, no changes needed
        // The suggested_alternative status can be used directly
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to reverse
    }
};
