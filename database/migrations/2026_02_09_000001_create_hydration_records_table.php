<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hydration_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->enum('liquid_type', ['water', 'infusion', 'juice', 'broth', 'other']);
            $table->unsignedInteger('amount_ml');
            $table->text('note')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['user_id', 'recorded_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hydration_records');
    }
};
