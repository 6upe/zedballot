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
        Schema::table('polls', function (Blueprint $table) {
            $table->string('nominee_registration_token')->nullable()->unique()->after('created_by');
            $table->string('voter_registration_token')->nullable()->unique()->after('nominee_registration_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn(['nominee_registration_token', 'voter_registration_token']);
        });
    }
};
