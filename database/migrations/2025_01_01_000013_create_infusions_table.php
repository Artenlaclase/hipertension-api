<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infusions', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // ej: "Té de hibisco"
            $table->text('description');                   // Descripción general
            $table->text('benefits')->nullable();          // Beneficios para HTA
            $table->text('preparation')->nullable();       // Modo de preparación
            $table->enum('precaution_level', [             // Semáforo de seguridad
                'safe',      // ✅ Segura – beneficiosa para HTA
                'caution',   // ⚠️ Precaución – consumir con moderación
                'avoid',     // 🔴 Evitar – puede elevar PA o interactuar con fármacos
            ])->default('safe');
            $table->text('precaution_note')->nullable();   // Nota de precaución específica
            $table->string('category')->default('herbal'); // herbal, tea, other
            $table->integer('recommended_ml')->default(250);
            $table->integer('max_daily_cups')->nullable();  // Máximo de tazas/día recomendadas
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infusions');
    }
};
