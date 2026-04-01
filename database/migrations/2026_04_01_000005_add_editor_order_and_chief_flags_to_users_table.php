<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('editor_order')->nullable()->after('can_access_archive');
            $table->boolean('is_chief_columnist')->default(false)->after('editor_order');
            $table->index('editor_order');
            $table->index('is_chief_columnist');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['editor_order']);
            $table->dropIndex(['is_chief_columnist']);
            $table->dropColumn(['editor_order', 'is_chief_columnist']);
        });
    }
};
