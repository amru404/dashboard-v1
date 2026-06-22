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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('sub_product_id')->nullable()->index()->constrained('products')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('license_type_id')->constrained('license_types')->restrictOnDelete();
            $table->text('license_key');
            $table->char('license_key_hash', 64)->unique();
            $table->string('client_name')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('max_activations')->nullable();
            $table->date('expired_date')->nullable()->index();
            $table->timestamps();

            $table->index(['user_id', 'product_id']);
            $table->index(['product_id', 'sub_product_id']);
            $table->index(['license_type_id', 'expired_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
