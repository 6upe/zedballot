<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('name');
            $table->text('description')->nullable();

            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();

            $table->enum('status', ['draft', 'active', 'closed', 'scheduled'])->default('draft');

            // Media
            $table->string('cover_image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('video')->nullable();

            // Voting Methods (comma-separated)
            $table->string('voting_methods')->nullable();

            // Eligibility
            $table->boolean('is_public')->default(true);
            $table->string('email_domain')->nullable();
            $table->string('country')->nullable();

            // Restrictions
            $table->boolean('allow_vote_edit')->default(false);

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
