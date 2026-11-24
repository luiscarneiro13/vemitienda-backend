<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_option_groups', function (Blueprint $table) {
            $table->boolean('is_removal')->default(false)->after('max_selections');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('image_url', 2048)->nullable()->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('product_option_groups', function (Blueprint $table) {
            $table->dropColumn('is_removal');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};
