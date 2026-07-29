<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionToPlaylistMetronomeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playlist_metronome', function (Blueprint $table) {
            $table->unsignedInteger('position')->default(0)->after('metronome_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playlist_metronome', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
}
