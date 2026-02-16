<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->string('status')->default('en attente');
            $table->float('score')->nullable();
            $table->date('date_evaluation')->nullable();
            $table->text('observation')->nullable();
            $table->foreignId('evaluateur_id')->nullable()->constrained('instructeurs')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['formation_id', 'participant_id']);
            $table->index(['formation_id']);
            $table->index(['participant_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qualifications');
    }
};
