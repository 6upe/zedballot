<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->enum('identifier_type', ['user_id', 'email'])->default('user_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('voters', function (Blueprint $table) {
            $table->enum('identifier_type', ['email'])->default('email')->change();
        });
    }
};
