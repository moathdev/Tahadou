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
            // MySQL cannot drop a unique index that is also used by a foreign key.
            // Drop the FK first, remove the unique index, then restore the FK.
            $table->dropForeign(['group_id']);
            $table->dropUnique(['group_id', 'phone_number']);
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->unique(['group_id', 'phone_number']);
            $table->foreign('group_id')->references('id')->on('groups')->cascadeOnDelete();
        });
    }
};
