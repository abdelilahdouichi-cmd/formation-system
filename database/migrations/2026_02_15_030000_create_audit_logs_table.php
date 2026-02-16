<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('method'); // POST, PUT, PATCH, DELETE
            $table->string('path');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('action'); // create, update, delete
            $table->string('model_type')->nullable(); // User, Post, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->unsignedSmallInteger('response_code');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
