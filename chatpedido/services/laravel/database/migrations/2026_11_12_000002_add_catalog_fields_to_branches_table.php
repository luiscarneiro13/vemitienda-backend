<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->foreignId('city_id')->nullable()->after('city')
                ->constrained('cities')->restrictOnDelete();
            $table->string('whatsapp_number', 20)->nullable()->after('phone');
        });

        DB::table('branches')->whereNull('uuid')->orderBy('id')->each(function ($branch) {
            DB::table('branches')->where('id', $branch->id)->update(['uuid' => (string) Str::uuid()]);
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropUnique(['uuid']);
            $table->dropColumn(['uuid', 'whatsapp_number']);
        });
    }
};
