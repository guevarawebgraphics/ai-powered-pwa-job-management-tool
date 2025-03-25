<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->longtext('youtube_link')->nullable();
        });
    }

    public function down()
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->dropColumn('youtube_link');
        });
    }
};
