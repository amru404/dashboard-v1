<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create pivot table
        Schema::create('license_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained('licenses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_owner')->default(false)->index();
            $table->foreignId('shared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('shared_at')->nullable();
            $table->timestamp('access_revoked_at')->nullable();
            $table->timestamps();

            $table->unique(['license_id', 'user_id']);
            $table->index(['user_id', 'is_owner']);
        });

        // Migrate existing data: every license->user relationship becomes pivot entry with is_owner=true
        DB::statement('
            INSERT INTO license_user (license_id, user_id, is_owner, shared_at, created_at, updated_at)
            SELECT id, user_id, true, created_at, created_at, updated_at
            FROM licenses
            WHERE user_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_user');
    }
};
