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
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropUnique('licenses_license_key_hash_unique');
            
            // Add index for performance (non-unique)
            $table->index('license_key_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->dropIndex(['license_key_hash']);
            
            // Restore unique constraint
            $table->unique('license_key_hash');
        });
    }
};
