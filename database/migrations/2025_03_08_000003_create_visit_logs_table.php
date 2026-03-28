<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('path', 500)->index();
            $table->string('ip', 45)->index();
            $table->string('user_agent', 512)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->timestamp('visited_at')->index();
            $table->timestamps();
        });

        Schema::table('visit_logs', function (Blueprint $table) {
            $table->index(['visited_at', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
