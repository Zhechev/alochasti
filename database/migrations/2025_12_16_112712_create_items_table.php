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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('city_id')->constrained()->restrictOnDelete();

            $table->string('title', 150);
            $table->text('description');

            $table->decimal('weight', 8, 2)->nullable();
            $table->string('dimensions', 100)->nullable();

            // Status is intentionally stored as a string enum for readability and portability.
            $table->enum('status', ['available', 'gifted'])->default('available')->index();
            $table->timestamp('gifted_at')->nullable()->index();

            $table->timestamps();

            $table->index(['category_id', 'status']);
            $table->index(['city_id', 'status']);
            $table->fullText(['title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};


