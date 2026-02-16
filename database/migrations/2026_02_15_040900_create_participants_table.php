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
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->foreignId('classe_id')->nullable()->constrained('classes')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('entreprise')->nullable();
            $table->string('poste')->nullable();
            $table->string('numero_identite')->nullable()->unique();
            $table->date('date_inscription');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['formation_id']);
            $table->index(['classe_id']);
            $table->index(['is_active']);
            $table->index(['date_inscription']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
