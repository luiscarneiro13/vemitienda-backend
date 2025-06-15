<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProviderColumnsToOauthAccessTokensAndUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_access_tokens,users', function (Blueprint $table) {
            Schema::table('oauth_access_tokens', function (Blueprint $table) {
                $table->string('provider')->nullable();
            });

            Schema::table('users', function (Blueprint $table) {
                $table->string('provider_user_id')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_access_tokens,users', function (Blueprint $table) {
            //
        });
    }
}
