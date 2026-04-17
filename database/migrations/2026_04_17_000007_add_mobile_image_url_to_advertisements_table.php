<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            if (!Schema::hasColumn('advertisements', 'mobile_image_url')) {
                $table->string('mobile_image_url')->nullable()->after('image_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('advertisements', function (Blueprint $table) {
            if (Schema::hasColumn('advertisements', 'mobile_image_url')) {
                $table->dropColumn('mobile_image_url');
            }
        });
    }
};
