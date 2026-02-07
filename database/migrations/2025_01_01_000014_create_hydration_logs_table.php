<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hydration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['water', 'infusion', 'other'])->default('water');
            $table->foreignId('infusion_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('amount_ml');                  // Cantidad en mililitros
            $table->timestamp('logged_at');                // Momento del consumo
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hydration_logs');
    }
};
