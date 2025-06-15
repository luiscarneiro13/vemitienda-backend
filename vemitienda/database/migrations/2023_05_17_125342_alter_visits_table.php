<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->text('ip_address')->change();
            $table->text('user_agent')->change();
            $table->text('referer')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visits', function (Blueprint $table) {
            //
        });
    }
}
