<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow the same phone number to appear multiple times per group.
     * Use case: a parent registering multiple children under their number.
     */
    public function up(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropUnique(['group_id', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->unique(['group_id', 'phone_number']);
        });
    }
};
