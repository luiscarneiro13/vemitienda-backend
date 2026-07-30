<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddSlugToPlaylistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->string('slug', 170)->nullable()->after('name');
        });

        // Backfill: cada playlist existente recibe un slug único derivado de su nombre.
        DB::table('playlists')->orderBy('id')->get(['id', 'name'])->each(function ($playlist) {
            $base = Str::slug($playlist->name, '-') ?: 'playlist';
            $slug = $base;
            $i = 2;

            while (DB::table('playlists')->where('slug', $slug)->where('id', '!=', $playlist->id)->exists()) {
                $slug = $base . '-' . $i;
                $i++;
            }

            DB::table('playlists')->where('id', $playlist->id)->update(['slug' => $slug]);
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->string('slug', 170)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
