<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_service_provider', function (Blueprint $table) {
            // Claves foráneas sin auto-incrementing ID ya que es una tabla pivote
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_provider_id')->constrained()->onDelete('cascade');

            // Definir una clave primaria compuesta para evitar duplicados y mejorar el rendimiento
            $table->primary(['category_id', 'service_provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_service_provider');
    }
};
