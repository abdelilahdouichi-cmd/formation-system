<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formation_examinateur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->foreignId('examinateur_id')->constrained('examinateurs')->cascadeOnDelete();
            $table->dateTime('date_examen')->nullable();
            $table->string('lieu')->nullable();
            $table->timestamps();

            $table->unique(['formation_id', 'examinateur_id']);
            $table->index(['formation_id']);
            $table->index(['examinateur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_examinateur');
    }
};
