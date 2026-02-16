<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('situation_sces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->string('sce_name');
            $table->integer('nombre_inscrits')->default(0);
            $table->integer('nombre_presents')->default(0);
            $table->integer('nombre_absents')->default(0);
            $table->integer('nombre_qualifies')->default(0);
            $table->integer('nombre_non_qualifies')->default(0);
            $table->integer('nombre_diplomes')->default(0);
            $table->integer('nombre_licences')->default(0);
            $table->date('date_rapport')->nullable();
            $table->text('observation')->nullable();
            $table->string('statut_rapport')->default('en cours');
            $table->timestamps();

            $table->index(['formation_id']);
            $table->index(['date_rapport']);
            $table->index(['statut_rapport']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('situation_sces');
    }
};
