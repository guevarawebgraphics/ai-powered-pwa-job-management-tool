<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->longtext('url')->nullable('Redirect PWA users to the URL Links');
        });
    }

    public function down()
    {
        Schema::table('notification', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
};
