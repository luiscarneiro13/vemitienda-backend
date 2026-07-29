<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeBpmNullableOnMetronomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('metronomes', function (Blueprint $table) {
            $table->unsignedSmallInteger('bpm')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('metronomes', function (Blueprint $table) {
            $table->unsignedSmallInteger('bpm')->default(120)->change();
        });
    }
}
