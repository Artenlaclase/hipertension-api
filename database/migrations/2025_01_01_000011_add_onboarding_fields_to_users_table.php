<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('initial_systolic')->nullable()->after('hta_level');
            $table->integer('initial_diastolic')->nullable()->after('initial_systolic');
            $table->text('food_restrictions')->nullable()->after('initial_diastolic');
            $table->boolean('onboarding_completed')->default(false)->after('food_restrictions');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'initial_systolic',
                'initial_diastolic',
                'food_restrictions',
                'onboarding_completed',
            ]);
        });
    }
};
