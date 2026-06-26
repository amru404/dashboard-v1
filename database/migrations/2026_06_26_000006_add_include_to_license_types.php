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
        Schema::table('license_types', function (Blueprint $table) {
            $table->boolean('include_in_packages')->default(true)->after('is_active')->comment('Whether this license type is included in package bundles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('license_types', function (Blueprint $table) {
            $table->dropColumn('include_in_packages');
        });
    }
};
