<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_instructeur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->foreignId('instructeur_id')->constrained('instructeurs')->cascadeOnDelete();
            $table->string('role')->default('instructeur');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->timestamps();

            $table->unique(['formation_id', 'instructeur_id']);
            $table->index(['formation_id']);
            $table->index(['instructeur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_instructeur');
    }
};
