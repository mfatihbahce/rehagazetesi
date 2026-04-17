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
            if (!Schema::hasColumn('users', 'legacy_user_id')) {
                $table->unsignedBigInteger('legacy_user_id')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'can_access_archive')) {
                $table->boolean('can_access_archive')->default(false)->after('legacy_user_id');
            }
        });

        if (!$indexExists('users', 'users_legacy_user_id_index')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('legacy_user_id');
            });
        }
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
            if ($indexExists('users', 'users_legacy_user_id_index')) {
                $table->dropIndex('users_legacy_user_id_index');
            }

            $dropColumns = [];
            if (Schema::hasColumn('users', 'legacy_user_id')) {
                $dropColumns[] = 'legacy_user_id';
            }
            if (Schema::hasColumn('users', 'can_access_archive')) {
                $dropColumns[] = 'can_access_archive';
            }
            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
