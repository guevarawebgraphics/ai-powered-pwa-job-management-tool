<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->string('is_seen')->default(0)->comment('0=not seen; 1=seen;');
        });
    }

    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('is_seen');
        });
    }
};
