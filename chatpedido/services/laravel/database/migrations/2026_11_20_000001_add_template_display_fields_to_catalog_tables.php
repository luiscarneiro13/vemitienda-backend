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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('compare_at_price', 10, 2)->nullable()->after('price');
            $table->string('badge', 60)->nullable()->after('compare_at_price');
            $table->boolean('is_featured')->default(false)->after('is_available');

            $table->index(['company_id', 'is_featured'], 'products_company_featured_idx');
        });

        Schema::table('catalog_categories', function (Blueprint $table) {
            $table->string('icon', 60)->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_categories', function (Blueprint $table) {
            $table->dropColumn('icon');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_company_featured_idx');
            $table->dropColumn(['compare_at_price', 'badge', 'is_featured']);
        });
    }
};
