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
    Schema::create('voters', function (Blueprint $table) {
        $table->id();

        $table->foreignId('poll_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->enum('identifier_type', ['email', 'phone', 'nrc', 'passport']);
        $table->string('identifier_value');

        $table->timestamp('verified_at')->nullable();

        $table->timestamps();

        // Prevent same identity voting twice in the same poll
        $table->unique(['poll_id', 'identifier_type', 'identifier_value']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};
