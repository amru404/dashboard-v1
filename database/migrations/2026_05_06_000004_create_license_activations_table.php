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
        Schema::create('license_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained('licenses')->cascadeOnDelete();
            $table->string('device_id');
            $table->string('ip_address', 45)->nullable();
            $table->string('hostname')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('active')->index();
            $table->timestamps();

            $table->unique(['license_id', 'device_id']);
            $table->index(['license_id', 'status']);
            $table->index(['device_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_activations');
    }
};
