<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('store_category', 100)->nullable()->index();
            $table->string('store_description', 250)->nullable();
            $table->string('cover_image_url', 2048)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['store_category']);
            $table->dropColumn(['store_category', 'store_description', 'cover_image_url']);
        });
    }
};
