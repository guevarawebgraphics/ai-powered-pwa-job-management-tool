<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('openai_files', function (Blueprint $table) {
            $table->string('json_response')->nullable();
        });
    }

    public function down()
    {
        Schema::table('openai_files', function (Blueprint $table) {
            $table->dropColumn('json_response');
        });
    }
};
