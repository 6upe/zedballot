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
        Schema::create('eligible_voters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('poll_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->nullable();
            $table->enum('identifier_type', ['email', 'phone', 'nrc', 'passport'])->nullable();
            $table->string('identifier_value')->nullable();

            $table->timestamp('registered_at')->nullable();

            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['poll_id', 'email']);
            $table->unique(['poll_id', 'phone']);
            $table->unique(['poll_id', 'identifier_type', 'identifier_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligible_voters');
    }
};
