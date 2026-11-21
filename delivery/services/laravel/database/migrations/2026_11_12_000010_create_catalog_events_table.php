<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('catalog_id')->nullable()->constrained('catalogs')->nullOnDelete();
            $table->string('event_type', 32); // 'view', 'order_sent'
            $table->unsignedInteger('total_cents')->default(0);
            $table->unsignedSmallInteger('items_count')->default(0);
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['company_id', 'event_type', 'created_at']);
            $table->index(['branch_id', 'event_type', 'created_at']);
            $table->index(['event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_events');
    }
};
