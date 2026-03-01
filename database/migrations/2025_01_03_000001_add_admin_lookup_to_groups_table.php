<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            // SHA-256 of the raw admin code — used for code-only login (no UUID needed)
            $table->string('admin_lookup')->nullable()->unique()->after('admin_code');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('admin_lookup');
        });
    }
};
