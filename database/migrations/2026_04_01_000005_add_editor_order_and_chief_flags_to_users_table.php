<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexExists = static function (string $table, string $index): bool {
            $database = DB::getDatabaseName();
            $result = DB::selectOne(
                'SELECT COUNT(*) AS cnt FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$database, $table, $index]
            );

            return (int) ($result->cnt ?? 0) > 0;
        };

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'editor_order')) {
                $table->unsignedInteger('editor_order')->nullable()->after('can_access_archive');
            }

            if (!Schema::hasColumn('users', 'is_chief_columnist')) {
                $table->boolean('is_chief_columnist')->default(false)->after('editor_order');
            }
        });

        Schema::table('users', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('users', 'users_editor_order_index')) {
                $table->index('editor_order');
            }
            if (!$indexExists('users', 'users_is_chief_columnist_index')) {
                $table->index('is_chief_columnist');
            }
        });
    }

    public function down(): void
    {
        $indexExists = static function (string $table, string $index): bool {
            $database = DB::getDatabaseName();
            $result = DB::selectOne(
                'SELECT COUNT(*) AS cnt FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
                [$database, $table, $index]
            );

            return (int) ($result->cnt ?? 0) > 0;
        };

        Schema::table('users', function (Blueprint $table) use ($indexExists) {
            if ($indexExists('users', 'users_editor_order_index')) {
                $table->dropIndex('users_editor_order_index');
            }
            if ($indexExists('users', 'users_is_chief_columnist_index')) {
                $table->dropIndex('users_is_chief_columnist_index');
            }

            $dropColumns = [];
            if (Schema::hasColumn('users', 'editor_order')) {
                $dropColumns[] = 'editor_order';
            }
            if (Schema::hasColumn('users', 'is_chief_columnist')) {
                $dropColumns[] = 'is_chief_columnist';
            }
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
