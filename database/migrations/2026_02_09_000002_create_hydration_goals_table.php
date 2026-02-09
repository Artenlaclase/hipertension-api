<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hydration_goals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('goal_ml')->default(2000);
            $table->date('effective_date');
            $table->timestamps();
            $table->unique(['user_id', 'effective_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('hydration_goals');
    }
};
