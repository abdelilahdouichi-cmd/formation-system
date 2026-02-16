<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->cascadeOnDelete();
            $table->foreignId('categorie_id')->nullable()->constrained('categories')->cascadeOnDelete();
            $table->foreignId('sous_categorie_id')->nullable()->constrained('sous_categories')->cascadeOnDelete();
            $table->string('nom');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->text('objectif')->nullable();
            $table->integer('duree_heures')->nullable();
            $table->decimal('prix', 10, 2)->nullable();
            $table->dateTime('date_debut_prevue')->nullable();
            $table->dateTime('date_fin_prevue')->nullable();
            $table->dateTime('date_debut_reelle')->nullable();
            $table->dateTime('date_fin_reelle')->nullable();
            $table->string('status')->default('planifiée');
            $table->string('lieu')->nullable();
            $table->integer('nombreParticipantsMin')->nullable();
            $table->integer('nombreParticipantsMax')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['niveau_id']);
            $table->index(['categorie_id']);
            $table->index(['sous_categorie_id']);
            $table->index(['status']);
            $table->index(['is_active']);
            $table->index(['date_debut_prevue']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
