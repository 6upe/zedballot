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
    Schema::create('votes', function (Blueprint $table) {
        $table->id();

        $table->foreignId('poll_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('category_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('nominee_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('voter_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->timestamps();

        // HARD RULE: One vote per category per voter per poll
        $table->unique(['poll_id', 'category_id', 'voter_id']);
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
