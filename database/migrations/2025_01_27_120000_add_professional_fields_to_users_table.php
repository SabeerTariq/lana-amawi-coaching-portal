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
        Schema::table('users', function (Blueprint $table) {
            $table->string('address')->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            $table->integer('age')->nullable()->after('gender');
            $table->json('languages_spoken')->nullable()->after('age');
            $table->string('institution_hospital')->nullable()->after('languages_spoken');
            $table->string('position')->nullable()->after('institution_hospital');
            $table->date('position_as_of_date')->nullable()->after('position');
            $table->string('specialty')->nullable()->after('position_as_of_date');
            $table->date('graduation_date')->nullable()->after('specialty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'date_of_birth',
                'gender',
                'age',
                'languages_spoken',
                'institution_hospital',
                'position',
                'position_as_of_date',
                'specialty',
                'graduation_date'
            ]);
        });
    }
};
