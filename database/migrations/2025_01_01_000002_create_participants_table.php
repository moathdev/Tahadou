<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->string('name');
            $table->string('phone_number');
            $table->json('interests');
            $table->foreignId('assigned_to_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->timestamps();

            // Phone number must be unique per group
            $table->unique(['group_id', 'phone_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
