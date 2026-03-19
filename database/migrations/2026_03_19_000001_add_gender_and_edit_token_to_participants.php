<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->enum('gender', ['male', 'female', 'child'])
                  ->default('male')
                  ->after('phone_number');

            $table->string('edit_token', 64)
                  ->nullable()
                  ->unique()
                  ->after('interests');
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropColumn('edit_token');
        });
    }
};
