<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('justificatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained('participants')->cascadeOnDelete();
            $table->foreignId('diplome_id')->nullable()->constrained('diplomes')->cascadeOnDelete();
            $table->foreignId('licence_id')->nullable()->constrained('licences')->cascadeOnDelete();
            $table->string('type');
            $table->string('nom_fichier');
            $table->string('chemin_fichier');
            $table->bigInteger('taille');
            $table->string('mime_type');
            $table->dateTime('date_upload');
            $table->text('description')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('instructeurs')->cascadeOnDelete();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->index(['formation_id']);
            $table->index(['participant_id']);
            $table->index(['diplome_id']);
            $table->index(['licence_id']);
            $table->index(['is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('justificatifs');
    }
};
