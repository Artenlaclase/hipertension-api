<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('educational_contents', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('level');
            $table->boolean('is_premium')->default(false)->after('order');
        });
    }

    public function down(): void
    {
        Schema::table('educational_contents', function (Blueprint $table) {
            $table->dropColumn(['order', 'is_premium']);
        });
    }
};
