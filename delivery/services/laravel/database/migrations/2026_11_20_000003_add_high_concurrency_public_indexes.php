<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->index(['slug', 'is_active', 'status', 'deleted_at'], 'companies_public_slug_active_idx');
            $table->index(['is_active', 'status', 'deleted_at', 'store_category'], 'companies_public_category_idx');
        });

        Schema::table('catalog_categories', function (Blueprint $table) {
            $table->index(['catalog_id', 'is_active', 'sort_order', 'name'], 'catalog_categories_public_order_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['catalog_category_id', 'is_active', 'sort_order', 'name'], 'products_public_order_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_public_order_idx');
        });

        Schema::table('catalog_categories', function (Blueprint $table) {
            $table->dropIndex('catalog_categories_public_order_idx');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex('companies_public_slug_active_idx');
            $table->dropIndex('companies_public_category_idx');
        });
    }
};
