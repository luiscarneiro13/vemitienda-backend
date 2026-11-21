<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->index(['is_active', 'status', 'deleted_at', 'name'], 'companies_public_directory_idx');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->index(['company_id', 'status'], 'branches_company_status_idx');
            $table->index(['company_id', 'is_main'], 'branches_company_main_idx');
        });

        Schema::table('catalogs', function (Blueprint $table) {
            $table->index(['company_id', 'is_default', 'is_active'], 'catalogs_company_default_active_idx');
        });

        Schema::table('catalog_categories', function (Blueprint $table) {
            $table->index(['company_id', 'sort_order'], 'catalog_categories_company_sort_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['catalog_category_id', 'is_active', 'sort_order'], 'products_category_active_sort_idx');
            $table->index(['company_id', 'sort_order'], 'products_company_sort_idx');
        });

        Schema::table('product_option_groups', function (Blueprint $table) {
            $table->index(['product_id', 'sort_order'], 'product_option_groups_product_sort_idx');
        });

        Schema::table('product_options', function (Blueprint $table) {
            $table->index(['product_option_group_id', 'is_active', 'sort_order'], 'product_options_group_active_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::table('product_options', function (Blueprint $table) {
            $table->dropIndex('product_options_group_active_sort_idx');
        });

        Schema::table('product_option_groups', function (Blueprint $table) {
            $table->dropIndex('product_option_groups_product_sort_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_active_sort_idx');
            $table->dropIndex('products_company_sort_idx');
        });

        Schema::table('catalog_categories', function (Blueprint $table) {
            $table->dropIndex('catalog_categories_company_sort_idx');
        });

        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropIndex('catalogs_company_default_active_idx');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex('branches_company_status_idx');
            $table->dropIndex('branches_company_main_idx');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex('companies_public_directory_idx');
        });
    }
};
