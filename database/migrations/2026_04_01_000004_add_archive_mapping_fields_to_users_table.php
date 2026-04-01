<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('legacy_user_id')->nullable()->after('role');
            $table->boolean('can_access_archive')->default(false)->after('legacy_user_id');
            $table->index('legacy_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['legacy_user_id']);
            $table->dropColumn(['legacy_user_id', 'can_access_archive']);
        });
    }
};
