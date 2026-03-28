<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->index(['status', 'is_breaking', 'published_at']);
            $table->index(['status', 'is_featured', 'published_at']);
        });

        if (\DB::getDriverName() === 'mysql') {
            \DB::statement('ALTER TABLE news ADD FULLTEXT INDEX news_search_idx (title, excerpt, content)');
        }
    }

    public function down(): void
    {
        if (\DB::getDriverName() === 'mysql') {
            \DB::statement('ALTER TABLE news DROP INDEX news_search_idx');
        }

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_breaking', 'published_at']);
            $table->dropIndex(['status', 'is_featured', 'published_at']);
        });
    }
};
