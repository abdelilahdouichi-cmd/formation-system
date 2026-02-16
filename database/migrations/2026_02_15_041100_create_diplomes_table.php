<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diplomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('numero_diplome')->unique()->nullable();
            $table->string('status')->default('à livrer');
            $table->date('date_delivrance')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('lieu_delivrance')->nullable();
            $table->boolean('signature_directeur')->default(false);
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->unique(['formation_id', 'participant_id']);
            $table->index(['formation_id']);
            $table->index(['participant_id']);
            $table->index(['status']);
            $table->index(['date_delivrance']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diplomes');
    }
};
